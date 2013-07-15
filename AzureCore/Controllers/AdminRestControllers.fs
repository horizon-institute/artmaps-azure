#light

namespace ArtMaps.Controllers

open ArtMaps.Persistence
open Org.BouncyCastle.Security
open Org.BouncyCastle.OpenSsl
open System.IO
open System.Linq
open System.Security.Cryptography
open System.Text
open System.Text.RegularExpressions
open System.Web.Http
open System.Web.Http.ModelBinding

module Conf = ArtMaps.Azure.Utilities.Configuration
module CTX = ArtMaps.Context
module E = Errors
module WU = ArtMaps.Utilities.Web

type ContextIn = {
    name : string
    signature : string
}

type ContextOut = {
    ID: int64
    name : string
    key : string
}

[<WU.AdminContext>]
type AdminController() =
    inherit ApiController()

    static let AllowedContextNameRegex = new Regex("^[a-z0-9]*$");
    
    [<HttpOptions>]
    [<ActionName("Context")>]
    member this.Options() = ()

    [<HttpPost>]
    [<ActionName("Context")>] 
    member this.CreateContext
            ([<ModelBinder(typeof<WU.ContextBinderProvider>)>]admin: CTX.t,
                ctx : ContextIn,
                [<ModelBinder(typeof<WU.EncodingBinderProvider>)>]enc : Encoding) =

        if ctx.name.Length > 256 
                || AllowedContextNameRegex.IsMatch(ctx.name) |> not then 
            raise (E.Forbidden(E.ForbiddenMinorCode.InvalidContextName))

        try
            if admin.verifySignature (enc.GetBytes(ctx.name)) (ctx.signature :> obj) |> not then
                raise (E.Forbidden(E.ForbiddenMinorCode.InvalidSignature))
        with 
            | :? HttpResponseException as e ->
                raise e
            | _ as e ->
                raise (E.Forbidden(E.ForbiddenMinorCode.InvalidSignature))

        if admin.dataContext.Contexts.Count(fun (c : Entities.Context) -> c.Name = ctx.name) > 0 then
            raise (E.Forbidden(E.ForbiddenMinorCode.ContextExists))
            
        use gen = new RSACryptoServiceProvider(Conf.value<int>("ArtMaps.Security.KeySize"))
        gen.PersistKeyInCsp <- false
        
        let context = new Entities.Context()
        context.ID <- admin.getNextID(context :> obj)
        context.Name <- ctx.name
        context.Key <- new System.Data.Linq.Binary(gen.ExportCspBlob(true))
        admin.dataContext.Contexts.InsertOnSubmit context
        admin.dataContext.SubmitChanges()   

        let kp = DotNetUtilities.GetRsaKeyPair(gen)
        let sw = new StringWriter()
        let pw = new PemWriter(sw)
        pw.WriteObject(kp.Private)

        { 
            ContextOut.ID = context.ID
            name = context.Name
            key = sw.ToString()
        }