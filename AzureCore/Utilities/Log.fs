#light

module ArtMaps.Utilities.Log

let Information message = System.Diagnostics.Trace.TraceInformation(message)

let Warning message = System.Diagnostics.Trace.TraceWarning(message)

let Error message = System.Diagnostics.Trace.TraceError(message)