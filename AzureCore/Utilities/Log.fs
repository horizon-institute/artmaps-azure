#light

module ArtMaps.Utilities.Log

let information message = System.Diagnostics.Trace.TraceInformation(message)

let warning message = System.Diagnostics.Trace.TraceWarning(message)

let error message = System.Diagnostics.Trace.TraceError(message)