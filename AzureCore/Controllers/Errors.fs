#light

module ArtMaps.Controllers.Errors

open System
open System.Web.Http

type ForbiddenMinorCode = 
    | Unspecified = -1
    | InvalidContextName = 0
    | InvalidEndpoint = 1 
    | InvalidSignature = 2
    | ContextExists = 3
    | Expired = 4

type NotFoundMinorCode =
    | Unspecified = -1

let Error (major : Net.HttpStatusCode) (minor : int)  =
    let rm = new Net.Http.HttpResponseMessage(major)
    rm.Content <- new Net.Http.StringContent(Convert.ToString(minor))
    new HttpResponseException(rm)

let Forbidden (code : ForbiddenMinorCode) = 
    Error Net.HttpStatusCode.Forbidden (int code)

let NotFound (code : NotFoundMinorCode) =
    Error Net.HttpStatusCode.NotFound (int code)