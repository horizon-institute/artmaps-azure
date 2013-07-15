#light

module ArtMaps.Utilities.Collections

open System

/// <summary>Slices a sequence into sequences of the specified length.</summary>
let slice (size : int) (sequence : seq<obj>) = 
    match sequence |> Seq.length with
        | l when l <= size -> seq { yield sequence }
        | l -> 
            let count = int (Math.Ceiling(float l / float size)) - 1
            seq {0..count} 
                |> Seq.map (
                    fun i -> sequence |> Seq.skip (i * size) |> Seq.truncate size)
