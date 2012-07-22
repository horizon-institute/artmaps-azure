#light

module ArtMaps.Utilities.Web
  
open ArtMaps.Persistence.Context
open Microsoft.FSharp.Reflection
open Newtonsoft.Json
open System
open System.Collections.Generic
open System.Linq
open System.Text
open System.Web.Http.Controllers
open System.Web.Http.Filters
open System.Web.Http.ModelBinding
open System.Web.Http.ModelBinding.Binders
    
module AU = ArtMaps.Azure.Utilities
module CTX = ArtMaps.Context
module R = Reflection


type AdminContextAttribute() = 
    inherit Attribute()


type RecordConverter() =
    inherit JsonConverter()
    
    override this.CanRead with get () = true
    override this.CanWrite with get () = true
    override this.CanConvert(t) = FSharpType.IsRecord(t)

    override this.WriteJson(writer, ob, serializer) = 
        writer.WriteStartObject()
        FSharpType.GetRecordFields(ob.GetType())
        |> Array.iter
            (fun f -> 
                let value = f.GetValue(ob, Array.empty<obj>)
                if value = null |> not then 
                    writer.WritePropertyName(f.Name)
                    match FSharpType.IsRecord(f.PropertyType) with
                        | false -> serializer.Serialize(writer, value)
                        | true -> this.WriteJson(writer, value, serializer)
            )
        writer.WriteEndObject()

    override this.ReadJson(reader, t, ob, serializer) = 
        let values = new Dictionary<string, obj>()
        while reader.Read() && (reader.TokenType = JsonToken.EndObject |> not) do
            let name = Convert.ToString(reader.Value)
            reader.Read() |> ignore
            let value = 
                match reader.TokenType with
                    | JsonToken.StartObject -> serializer.Deserialize(reader)
                    | JsonToken.StartArray -> serializer.Deserialize(reader)
                    | JsonToken.Integer -> Convert.ToInt64(reader.Value) :> obj
                    | JsonToken.Float -> Convert.ToDouble(reader.Value) :> obj
                    | JsonToken.String -> Convert.ToString(reader.Value) :> obj
                    | JsonToken.Boolean -> Convert.ToBoolean(reader.Value) :> obj
                    | JsonToken.Null -> null
                    | x -> 
                        sprintf "Unable to process JsonType %s" (x.ToString()) |> Log.Warning
                        null
            values.Add(name, value)

        let getValue (field : Reflection.PropertyInfo) =
            match values.ContainsKey(field.Name) with
                | true -> values.[field.Name]
                | false ->
                    match field with
                        | f when R.IsListType(field.PropertyType) -> 
                            let empty = f.PropertyType.GetProperty("Empty")
                            empty.GetValue(null, [||])
                        | _ -> null

        let cons = FSharpValue.PreComputeRecordConstructor(t)
        let fields = FSharpType.GetRecordFields(t)
        let args = fields |> Array.map getValue
        cons args
  

type RecordBinder() =
    inherit MutableObjectModelBinder()
        
    let rec bindRecordModel (bc : ModelBindingContext) (t : Type) (p : string) : obj =     
        let cons = FSharpValue.PreComputeRecordConstructor(t)
        let fields = FSharpType.GetRecordFields(t)
        let args = 
            fields 
            |> Array.map 
                (fun f -> 
                    match FSharpType.IsRecord(f.PropertyType) with
                        | true -> 
                            bindRecordModel bc f.PropertyType (sprintf "%s%s." p f.Name)
                        | false ->
                            let vpr = bc.ValueProvider.GetValue(sprintf "%s%s" p f.Name)
                            match vpr with
                                | null -> null
                                | _ -> 
                                    match R.IsOptionType(f.PropertyType) with
                                        | false -> vpr.ConvertTo(f.PropertyType)
                                        | true -> 
                                            let ot = (f.PropertyType.GetGenericArguments()).[0]
                                            let go = R.GenericOptionType.MakeGenericType([| ot |])
                                            let con = go.GetConstructor([| ot |])
                                            con.Invoke([| vpr.ConvertTo(ot) |])
                )
        cons args

    override this.BindModel(actionContext, bindingContext) =
        match FSharpType.IsRecord(bindingContext.ModelType) with
            | false -> base.BindModel(actionContext, bindingContext)
            | true -> 
                bindingContext.Model <- bindRecordModel bindingContext bindingContext.ModelType ""
                true


type RecordBinderProvider() =
    inherit ModelBinderProvider()
    override this.GetBinder(configuration, bindingContext) =
        match FSharpType.IsRecord(bindingContext.ModelType) with
            | true -> new RecordBinder() :> IModelBinder
            | false -> null


type ContextBinder() =
    inherit MutableObjectModelBinder()  
    override this.BindModel(actionContext, bindingContext) =
        let cnt = actionContext.ControllerContext.Controller
        let ctx = new ModelDataContext(
                    AU.Configuration.Value<string>("ArtMaps.SqlServer.ConnectionString"))
        bindingContext.Model <- 
            try
                cnt.GetType().GetCustomAttributes(typeof<AdminContextAttribute>, false).Single() |> ignore
                CTX.CreateAdminContext AU.Resources.MasterKey ctx :> obj
            with _ ->
                let name = bindingContext.ValueProvider.GetValue("context").ConvertTo(typeof<string>) :?> string
                match CTX.CreateServiceContext name ctx with
                    | Some(c) -> c :> obj
                    | None -> null
        true


type ContextBinderProvider() =
    inherit ModelBinderProvider()
    override this.GetBinder(configuration, bindingContext) =
        match bindingContext.ModelType.IsAssignableFrom(typeof<CTX.t>) with
            | true -> new ContextBinder() :> IModelBinder
            | false -> null


type ValidContextFilter() =
    inherit ActionFilterAttribute()
    override this.OnActionExecuting(ctx : HttpActionContext) =
        let t = typeof<CTX.t>
        ctx.ActionDescriptor.GetParameters() 
        |> Seq.filter (fun p -> p.ParameterType.Equals(t))
        |> Seq.iter 
            (fun a -> 
            if ctx.ActionArguments.[a.ParameterName] = null then
                ctx.Response <- new Net.Http.HttpResponseMessage(Net.HttpStatusCode.NotFound))


type EncodingBinder() =
    inherit MutableObjectModelBinder()  
    override this.BindModel(actionContext, bindingContext) =
        bindingContext.Model <- 
            try Encoding.GetEncoding(actionContext.Request.Content.Headers.ContentType.CharSet)
            with _ -> Encoding.GetEncoding("ISO-8859-1")
        true


type EncodingBinderProvider() =
    inherit ModelBinderProvider()
    override this.GetBinder(configuration, bindingContext) =
        match bindingContext.ModelType.IsAssignableFrom(typeof<Encoding>) with
            | true -> new EncodingBinder() :> IModelBinder
            | false -> null