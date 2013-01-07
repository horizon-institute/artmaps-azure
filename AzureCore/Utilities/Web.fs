#light

module ArtMaps.Utilities.Web
  
open ArtMaps.Persistence.Context
open Microsoft.FSharp.Reflection
open Newtonsoft.Json
open System
open System.Collections.Generic
open System.Linq
open System.Net.Http.Headers
open System.Text
open System.Threading.Tasks
open System.Web.Http.Controllers
open System.Web.Http.Filters
open System.Web.Http.ModelBinding
open System.Web.Http.ModelBinding.Binders
    
module AU = ArtMaps.Azure.Utilities
module CTX = ArtMaps.Context
module R = Reflection

type JsonRecordConverter() =
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
                        sprintf "Unable to process JsonType %s" (x.ToString()) |> Log.warning
                        null
            values.Add(name, value)

        let getValue (field : Reflection.PropertyInfo) =
            match values.ContainsKey(field.Name) with
                | true -> values.[field.Name]
                | false ->
                    match field with
                        | f when R.isListType(field.PropertyType) -> 
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
                                    match R.isOptionType(f.PropertyType) with
                                        | false -> vpr.ConvertTo(f.PropertyType)
                                        | true -> 
                                            let ot = (f.PropertyType.GetGenericArguments()).[0]
                                            R.makeOption ot (vpr.ConvertTo(ot))
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
    override this.GetBinder(configuration, modelType) =
        match FSharpType.IsRecord(modelType) with
            | true -> new RecordBinder() :> IModelBinder
            | false -> null


type AdminContextAttribute() = 
    inherit Attribute()


type ContextBinder() =
    inherit MutableObjectModelBinder()  
    override this.BindModel(actionContext, bindingContext) =
        let cnt = actionContext.ControllerContext.Controller
        let ctx = new ModelDataContext(
                    AU.Configuration.value<string>("ArtMaps.SqlServer.ConnectionString"))
        bindingContext.Model <- 
            if cnt.GetType().GetCustomAttributes(typeof<AdminContextAttribute>, true).Any() then
                CTX.forAdmin AU.Resources.MasterKey ctx :> obj
            else
                let name = bindingContext.ValueProvider.GetValue("context").ConvertTo(typeof<string>) :?> string
                match CTX.forService name ctx with
                    | Some(c) -> c :> obj
                    | None -> null
        actionContext.Request.Properties.Add("context", bindingContext.Model)
        true

type MvcContextBinder() =
    interface System.Web.Mvc.IModelBinder with
        member this.BindModel(controllerContext, bindingContext) =
            let cnt = controllerContext.Controller
            let ctx = new ModelDataContext(
                        AU.Configuration.value<string>("ArtMaps.SqlServer.ConnectionString"))
            if cnt.GetType().GetCustomAttributes(typeof<AdminContextAttribute>, true).Any() then
                CTX.forAdmin AU.Resources.MasterKey ctx :> obj
            else
                let name = bindingContext.ValueProvider.GetValue("context").ConvertTo(typeof<string>) :?> string
                match CTX.forService name ctx with
                    | Some(c) -> c :> obj
                    | None -> null

type ContextBinderProvider() =
    inherit ModelBinderProvider()
    override this.GetBinder(configuration, modelType) =
        match modelType.IsAssignableFrom(typeof<CTX.t>) with
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


type DepthBinder() =
    inherit MutableObjectModelBinder()  
    override this.BindModel(actionContext, bindingContext) =
        bindingContext.Model <- 
            try 
                let h = actionContext.Request.Headers.Accept |> Seq.head
                let v = h.Parameters.Single(fun n -> n.Name = "depth")
                let i = Convert.ToInt32(v.Value)
                if i < 1 then 1 else i
            with _ -> 1
        true


type DepthBinderProvider() =
    inherit ModelBinderProvider()
    override this.GetBinder(configuration, modelType) =
        match modelType.IsAssignableFrom(typeof<Int32>) with
            | true -> new DepthBinder() :> IModelBinder
            | false -> null


type EncodingBinder() =
    inherit MutableObjectModelBinder()  
    override this.BindModel(actionContext, bindingContext) =
        bindingContext.Model <- 
            try Encoding.GetEncoding(actionContext.Request.Content.Headers.ContentType.CharSet)
            with _ -> Encoding.GetEncoding("ISO-8859-1")
        true


type EncodingBinderProvider() =
    inherit ModelBinderProvider()
    override this.GetBinder(configuration, modelType) =
        match modelType.IsAssignableFrom(typeof<Encoding>) with
            | true -> new EncodingBinder() :> IModelBinder
            | false -> null


type ExceptionLoggingFilter() =
    inherit ExceptionFilterAttribute()
    override this.OnException(context) =
        let e = context.Exception
        sprintf "%s\n%s" e.Message e.StackTrace |> Log.error


type CacheHeaderFilter(seconds : int64) =
    inherit ActionFilterAttribute()
    new(days : int, hours : int, minutes : int, seconds : int) = 
        new CacheHeaderFilter(((((((int64 days) * 24L) + (int64 hours)) * 60L) + (int64 minutes)) * 60L) + (int64 seconds))
    override this.OnActionExecuted(ctx) =
        try
            let h = new CacheControlHeaderValue()
            h.MaxAge <- new Nullable<TimeSpan>(TimeSpan.FromSeconds(float seconds))
            ctx.Response.Headers.CacheControl <- h
        with _ as e ->
            sprintf "Unable to set cache response header: %s\n%s" e.Message e.StackTrace |> Log.warning
