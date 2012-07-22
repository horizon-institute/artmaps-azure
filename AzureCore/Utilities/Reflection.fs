#light

module ArtMaps.Utilities.Reflection

open System

let GenericOptionType = (typeof<Option<_>>).GetGenericTypeDefinition()

let GenericListType = (typeof<List<_>>).GetGenericTypeDefinition()

let IsGenericAssignable (gt : Type) (t : Type) =
    match t.IsGenericType with
        | false -> false
        | true -> 
            t.GetGenericTypeDefinition()
                    .IsAssignableFrom(gt)

let IsOptionType = IsGenericAssignable GenericOptionType

let IsListType = IsGenericAssignable GenericListType