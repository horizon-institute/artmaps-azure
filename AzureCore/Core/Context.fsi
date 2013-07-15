#light

module ArtMaps.Context

open ArtMaps.Persistence.Context

type t = {
    ID : int64
    name : string
    dataContext : ModelDataContext
    verifySignature : byte[] -> obj -> bool
    getNextID : obj -> int64
}
    
val forAdmin : byte[] -> ModelDataContext -> bool -> t
    
val forService : string -> ModelDataContext -> bool -> t option