#light

module ArtMaps.Context

open ArtMaps.Persistence.Context
open ArtMaps.Persistence.Entities
open System
open System.Linq
open System.Security.Cryptography

type t = {
    ID : int64
    name : string
    dataContext : ModelDataContext
    verifySignature : byte[] -> obj -> bool
    getNextID : obj -> int64
}

let verify (key : byte[]) (data : byte[]) (signature : obj) = 
    use rsa = new RSACryptoServiceProvider()
    rsa.PersistKeyInCsp <- false
    rsa.ImportCspBlob(key)
    let sha = "SHA256"
    match signature with
        | :? string as sign -> 
            let s = Convert.FromBase64String(sign)
            rsa.VerifyData(data, sha, s)
        | :? (byte[]) as sign -> rsa.VerifyData(data, sha, sign)
        | _ -> raise (new NotSupportedException("Unknown signature type"))

let forAdmin (key : byte[]) (ctx : ModelDataContext) (emulated : bool) = 
    ctx.Connection.Open()
    if not emulated then
        ctx.ExecuteCommand("USE FEDERATION ContextFederation(ContextID=0) WITH RESET, FILTERING=OFF", [||]) |> ignore
    { 
        ID = int64 -1
        t.name = null
        dataContext = ctx
        verifySignature = verify key
        getNextID = (fun o -> ctx.GetNextID(o))
    }

let forService (name : string) (ctx : ModelDataContext) (emulated : bool) = 
    ctx.Connection.Open()
    if not emulated then
        ctx.ExecuteCommand("USE FEDERATION ContextFederation(ContextID=0) WITH RESET, FILTERING=OFF", [||]) |> ignore
    match ctx.Contexts.SingleOrDefault(fun (c : Context) -> c.Name = name) with
        | null ->  None
        | _ as c ->
            if not emulated then
                ctx.ExecuteCommand(sprintf "USE FEDERATION ContextFederation(ContextID=%i) WITH RESET, FILTERING=ON" c.ID, [||]) |> ignore
            Some({ 
                    t.ID = c.ID
                    name = name
                    dataContext = ctx
                    verifySignature = verify (c.Key.ToArray())
                    getNextID = 
                        (fun o -> 
                            if not emulated then
                                ctx.ExecuteCommand("USE FEDERATION ContextFederation(ContextID=0) WITH RESET, FILTERING=OFF", [||]) |> ignore
                            try
                                ctx.GetNextID(o)
                            finally
                                if not emulated then
                                    ctx.ExecuteCommand(sprintf "USE FEDERATION ContextFederation(ContextID=%i) WITH RESET, FILTERING=ON" c.ID, [||]) |> ignore
                        )
            })