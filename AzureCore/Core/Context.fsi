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
    
val forAdmin : byte[] -> ModelDataContext -> t
    
val forService : string -> ModelDataContext -> t option