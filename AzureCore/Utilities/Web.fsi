#light

module ArtMaps.Utilities.Web
    
/// JSON Converter for F# Record types.
/// Can both read and write.
type JsonRecordConverter =
    inherit Newtonsoft.Json.JsonConverter
    new : unit -> JsonRecordConverter

/// Value binder for F# Record types.
type RecordBinder =
    inherit System.Web.Http.ModelBinding.Binders.MutableObjectModelBinder
    new : unit -> RecordBinder

/// Value binder provider for F# Record types.
type RecordBinderProvider =
    inherit System.Web.Http.ModelBinding.ModelBinderProvider
    new : unit -> RecordBinderProvider

/// Attribute used to notify the ContextBinder that the
/// bound context should be an Admin context and not a
/// Service context.
type AdminContextAttribute =
    inherit System.Attribute
    new : unit -> AdminContextAttribute

/// Value binder for Contexts.
type ContextBinder =
    inherit System.Web.Http.ModelBinding.Binders.MutableObjectModelBinder
    new : unit -> ContextBinder

// Value binder for Contexts in the MVC framework.
type MvcContextBinder = 
    interface System.Web.Mvc.IModelBinder
    new : unit -> MvcContextBinder

/// Value binder provider for Contexts.
type ContextBinderProvider =
    inherit System.Web.Http.ModelBinding.ModelBinderProvider
    new : unit -> ContextBinderProvider

/// Web API filter, checks that a valid context has been
/// assigned, if not a 404 error will be thrown.
type ValidContextFilter =
    inherit System.Web.Http.Filters.ActionFilterAttribute
    new : unit -> ValidContextFilter

/// Value binder for depth, defaults to 0.
type DepthBinder =
    inherit System.Web.Http.ModelBinding.Binders.MutableObjectModelBinder
    new : unit -> DepthBinder
    
/// Value binder provider depth.
type DepthBinderProvider =
    inherit System.Web.Http.ModelBinding.ModelBinderProvider
    new : unit -> DepthBinderProvider

/// Value binder for document encoding, defaults to ISO-8859-1.
type EncodingBinder =
    inherit System.Web.Http.ModelBinding.Binders.MutableObjectModelBinder
    new : unit -> EncodingBinder
    
/// Value binder provider for document encoding.
type EncodingBinderProvider =
    inherit System.Web.Http.ModelBinding.ModelBinderProvider
    new : unit -> EncodingBinderProvider

/// Filter for logging exceptions.
type ExceptionLoggingFilter =
    inherit System.Web.Http.Filters.ExceptionFilterAttribute
    new : unit -> ExceptionLoggingFilter

/// Filter that sets the cache timeout header.
type CacheHeaderFilter =
    inherit System.Web.Http.Filters.ActionFilterAttribute
    new : int64 -> CacheHeaderFilter    
    new : int * int * int * int -> CacheHeaderFilter
