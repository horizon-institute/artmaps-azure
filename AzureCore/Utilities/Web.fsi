#light

module ArtMaps.Utilities.Web
    
type AdminContextAttribute =
    inherit System.Attribute
    new : unit -> AdminContextAttribute

type RecordConverter =
    inherit Newtonsoft.Json.JsonConverter
    new : unit -> RecordConverter

type RecordBinder =
    inherit System.Web.Http.ModelBinding.Binders.MutableObjectModelBinder
    new : unit -> RecordBinder

type RecordBinderProvider =
    inherit System.Web.Http.ModelBinding.ModelBinderProvider
    new : unit -> RecordBinderProvider

type ContextBinder =
    inherit System.Web.Http.ModelBinding.Binders.MutableObjectModelBinder
    new : unit -> ContextBinder

type ContextBinderProvider =
    inherit System.Web.Http.ModelBinding.ModelBinderProvider
    new : unit -> ContextBinderProvider

type ValidContextFilter =
    inherit System.Web.Http.Filters.ActionFilterAttribute
    new : unit -> ValidContextFilter

type EncodingBinder =
    inherit System.Web.Http.ModelBinding.Binders.MutableObjectModelBinder
    new : unit -> EncodingBinder
    
type EncodingBinderProvider =
    inherit System.Web.Http.ModelBinding.ModelBinderProvider
    new : unit -> EncodingBinderProvider