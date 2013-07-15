#light

module ArtMaps.Controllers.Errors

type ForbiddenMinorCode = 
    | Unspecified = -1
    | InvalidContextName = 0
    | InvalidEndpoint = 1 
    | InvalidSignature = 2
    | ContextExists = 3
    | Expired = 4

type NotFoundMinorCode =
    | Unspecified = -1

val Forbidden : ForbiddenMinorCode -> System.Web.Http.HttpResponseException

val NotFound : NotFoundMinorCode -> System.Web.Http.HttpResponseException
