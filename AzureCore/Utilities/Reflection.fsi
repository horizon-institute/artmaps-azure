#light

module ArtMaps.Utilities.Reflection

val isOptionType : (System.Type -> bool)

val isListType : (System.Type -> bool)

val makeOption : System.Type -> obj -> obj