#light

module ArtMaps.Azure.Utilities

module Configuration = 

    type DevFabricTraceListener =
        inherit System.Diagnostics.TraceListener
        new : string * string -> DevFabricTraceListener   
            
    val inline Value<'T> : string -> 'T

    val Settings : unit -> unit

    val Diagnostics : unit -> unit

    val Storage : unit -> unit

module Resources =
    
    val MasterKey : byte[]

    val Resource : string -> System.IO.Stream