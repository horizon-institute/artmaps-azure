#light

namespace ArtMaps.Controllers

open ArtMaps.Persistence
open ArtMaps.Persistence.Context
open Microsoft.FSharp.Linq
open Org.BouncyCastle.Security
open Org.BouncyCastle.OpenSsl
open System
open System.IO
open System.Linq
open System.Security.Cryptography
open System.Text
open System.Web.Http
open System.Web.Http.ModelBinding

module Conf = ArtMaps.Azure.Utilities.Configuration
module CTX = ArtMaps.Context
module E = Errors
module Log = ArtMaps.Utilities.Log
module Res = ArtMaps.Azure.Utilities.Resources
module WU = ArtMaps.Utilities.Web

type Context = {
    ID: int64;
    name : string;
    endpoint : string;
    signature : string;
    key : string;
}

[<WU.AdminContext>]
type AdminController() =
    inherit ApiController()

    let AllowedContextNameRegex = new RegularExpressions.Regex("^[a-z0-9]*$");
    
    [<HttpOptions>]
    [<ActionName("Context")>]
    member this.Options() = ()

    [<HttpPost>]
    [<ActionName("Context")>] 
    member this.CreateContext
            ([<ModelBinder(typeof<WU.ContextBinderProvider>)>]admin: CTX.t,
                ctx : Context,
                [<ModelBinder(typeof<WU.EncodingBinderProvider>)>]enc : Encoding) =

        if ctx.name.Length > 256 
                || AllowedContextNameRegex.IsMatch(ctx.name) |> not then 
            raise (E.Forbidden(E.ForbiddenMinorCode.InvalidContextName))

        if Uri.IsWellFormedUriString(ctx.endpoint, UriKind.Absolute) |> not then 
            raise (E.Forbidden(E.ForbiddenMinorCode.InvalidEndpoint))

        try
            let data = enc.GetBytes(sprintf "%s%s" ctx.endpoint ctx.name)
            if admin.verifySignature data (ctx.signature :> obj) |> not then
                raise (E.Forbidden(E.ForbiddenMinorCode.InvalidSignature))
        with 
            | :? HttpResponseException as e ->
                raise e
            | _ as e ->
                raise (E.Forbidden(E.ForbiddenMinorCode.InvalidSignature))

        if admin.dataContext.Contexts.Count(fun (c : Entities.Context) -> c.Name = ctx.name) > 0 then
            raise (E.Forbidden(E.ForbiddenMinorCode.ContextExists))
            
        use gen = new RSACryptoServiceProvider(Conf.Value<int>("ArtMaps.Security.KeySize"))
        gen.PersistKeyInCsp <- false
        
        let context = new Entities.Context()
        context.ID <- admin.dataContext.GetNextID(context)
        context.Endpoint <- ctx.endpoint
        context.Name <- ctx.name
        context.Key <- new Data.Linq.Binary(gen.ExportCspBlob(true))
        admin.dataContext.Contexts.InsertOnSubmit context
        admin.dataContext.SubmitChanges()   

        let kp = DotNetUtilities.GetRsaKeyPair(gen)
        let sw = new StringWriter()
        let pw = new PemWriter(sw)
        pw.WriteObject(kp.Private)

        { 
            Context.ID = context.ID
            name = context.Name
            endpoint = context.Endpoint
            signature = null
            key = sw.ToString()
        }