#light

module ArtMaps.Azure.Utilities

module Configuration = 
     
    val inline value<'T> : string -> 'T

module Resources =
    
    val MasterKey : byte[]

    val readOnly : string -> System.IO.Stream
