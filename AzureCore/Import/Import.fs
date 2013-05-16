#light

module ArtMaps.Import.Utilities

open ArtMaps.Persistence.Context
open ArtMaps.Persistence.Entities
open DataAccess
open Microsoft.SqlServer.Types
open Microsoft.WindowsAzure
open Microsoft.WindowsAzure.ServiceRuntime
open Microsoft.WindowsAzure.StorageClient
open System
open System.Linq

module AU = ArtMaps.Azure.Utilities
module Ctx = ArtMaps.Context
module Log = ArtMaps.Utilities.Log

type t = {
    queue : CloudQueue
    container : CloudBlobContainer
    item : CloudQueueMessage option
}


type SetupNotCompleteException =
    inherit Exception
    new () = { inherit Exception() }
    new (msg) = { inherit Exception(msg) }
    new (msg : string, e) = { inherit Exception(msg, e) }
    

let init (storage : CloudStorageAccount) =
    let v = {
        t.queue = storage.CreateCloudQueueClient().GetQueueReference("import")
        container = storage.CreateCloudBlobClient().GetContainerReference("import")
        item = None
    }
    try
        v.queue.FetchAttributes()    
    with _ as e ->
        raise (new SetupNotCompleteException("Queue 'import' has not been created", e))
    try
        v.container.FetchAttributes()    
    with _ as e ->
        raise (new SetupNotCompleteException("Blob container 'import' has not been created", e))
    v


let parseRow (ctx : Ctx.t) (number : int) (r : Row) = 
    sprintf "Importing row number %i" number |> Log.information
    let lat = Convert.ToDouble(r.Values.[0])
    let lng = Convert.ToDouble(r.Values.[1])
    let uri = sprintf "artmaps://%s" r.Values.[2]

    let o = 
        match ctx.dataContext.ObjectOfInterests.SingleOrDefault(fun (o : ObjectOfInterest) -> o.URI = uri) with
        | null -> 
            let o = new ObjectOfInterest()
            o.ID <- ctx.getNextID(o :> obj)
            o.URI <- uri
            o.ContextID <- ctx.ID
            ctx.dataContext.ObjectOfInterests.InsertOnSubmit(o)
            let p = new LocationPoint()
            p.ID <- ctx.getNextID(p :> obj)
            p.Error <- 0L
            p.Center <- SqlGeography.Point(lat, lng, 4326)
            ctx.dataContext.LocationPoints.InsertOnSubmit(p)
            let l = new Location()
            l.ID <- ctx.getNextID(l :> obj)
            l.LocationSource <- LocationSource.SystemImport
            l.ObjectOfInterest <- o
            l.LocationPoints.Add(p)
            ctx.dataContext.Locations.InsertOnSubmit(l)
            o
        | o -> 
            let lp = o.Locations.[0].LocationPoint
            lp.Center <- SqlGeography.Point(lat, lng, 4326)
            ctx.dataContext.ObjectMetadatas.DeleteAllOnSubmit(o.ObjectMetadatas)
            o

    r.Values 
    |> Seq.skip 3 
    |> Seq.iteri (
        fun i v -> 
            let md = new ObjectMetadata()
            md.ID <- ctx.getNextID(md :> obj)
            md.Name <- r.ColumnNames.ElementAt(i + 3)
            md.Value <- v
            md.ValueType <- MetadataValueType.TextPlain
            ctx.dataContext.ObjectMetadatas.InsertOnSubmit(md)
            o.ObjectMetadatas.Add(md)
            ())


let import (current : t) =
    match current.item with
        | None -> ()
        | Some(msg) ->
            sprintf "Dequeuing message on import queue with ID %s" msg.Id |> Log.information
            let mdx = new ModelDataContext(
                        AU.Configuration.value<string>("ArtMaps.SqlServer.ConnectionString"))
            let blob = current.container.GetBlockBlobReference(msg.AsString)
            try
                blob.FetchAttributes()
                let ctx = Ctx.forService blob.Metadata.["ContextName"] mdx RoleEnvironment.IsEmulated
                match ctx with
                    | None -> 
                        current.queue.DeleteMessage(msg)
                        blob.DeleteIfExists() |> ignore
                        mdx.Dispose()
                        // Report error
                    | Some(c) ->
                        let table = DataTable.New.Read(new IO.StreamReader(blob.OpenRead(), Text.Encoding.UTF8))
                        table.Rows |> Seq.iteri (parseRow c)
                        mdx.SubmitChanges()
                        current.queue.DeleteMessage(msg)
                        blob.DeleteIfExists() |> ignore
                        mdx.Dispose()
                        // report success
            with e ->
                sprintf "%s:\n%s" e.Message e.StackTrace |> Log.error
                current.queue.DeleteMessage(msg)
                blob.DeleteIfExists() |> ignore
                mdx.Dispose()
                // report error
            

              
