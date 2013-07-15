#light

module ArtMaps.Azure.Utilities

module Cache =
    
    val clearMetadata : unit -> unit

module Configuration = 

    type DevFabricTraceListener =
        inherit System.Diagnostics.TraceListener
        new : string * string -> DevFabricTraceListener   
            
    val inline value<'T> : string -> 'T

    val initSettings : unit -> unit

    val initDiagnostics : unit -> unit

    val initStorage : unit -> unit

module Resources =
    
    val MasterKey : byte[]

    val readOnly : string -> System.IO.Stream
