#light

module ArtMaps.Utilities.Reflection

open System

let GenericOptionType = (typeof<Option<_>>).GetGenericTypeDefinition()

let GenericListType = (typeof<List<_>>).GetGenericTypeDefinition()

let isGenericAssignable (gt : Type) (t : Type) =
    match t.IsGenericType with
        | false -> false
        | true -> 
            t.GetGenericTypeDefinition()
                    .IsAssignableFrom(gt)

let isOptionType = isGenericAssignable GenericOptionType

let isListType = isGenericAssignable GenericListType

let makeOption (t : Type) (value : obj) =
    let ot = GenericOptionType.MakeGenericType([| t |])
    let con = ot.GetConstructor([| t |])
    con.Invoke([| value |])