#light 

namespace ArtMaps.Controllers

open ArtMaps.Persistence.Context
open ArtMaps.Persistence.Entities
open DataAccess
open Microsoft.SqlServer.Types
open Microsoft.WindowsAzure.Storage
open Microsoft.WindowsAzure.Storage.Queue
open System
open System.IO
open System.Linq
open System.Web
open System.Web.Mvc

module C = ArtMaps.Azure.Utilities.Configuration
module CTX = ArtMaps.Context
module Er = Errors
module Log = ArtMaps.Utilities.Log
module WU = ArtMaps.Utilities.Web

type CsvImportController() =
    inherit Controller()

    let parseRow (ctx : CTX.t) (r : Row) = 
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
                ctx.dataContext.SubmitChanges() |> ignore
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
                ctx.dataContext.SubmitChanges() |> ignore
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
                
        ctx.dataContext.SubmitChanges()

    [<HttpPost>]
    member this.Import 
        ([<ModelBinder(typeof<WU.MvcContextBinder>)>]context : CTX.t,
            signature: string,
            callback: string,
            file : HttpPostedFileBase) = 

        Log.information "Doing import"

        let ms = new MemoryStream(file.ContentLength)
        file.InputStream.CopyTo(new BufferedStream(ms))

        let b = ms.GetBuffer()
        
        if context.verifySignature b (signature :> obj) |> not then
            raise (Er.Forbidden(Er.ForbiddenMinorCode.InvalidSignature))

        let cs : string = C.value("ArtMaps.Storage.ConnectionString")
        let sa = 
            if cs.ToLower().Contains("usedevelopmentstorage=true") then
                CloudStorageAccount.DevelopmentStorageAccount
            else
                CloudStorageAccount.Parse(cs)
        
        let bc = sa.CreateCloudBlobClient()
        let con = bc.GetContainerReference("import")
        let id = System.Guid.NewGuid().ToString()
        let blob = con.GetBlockBlobReference(id)        
        blob.UploadFromStream(new BufferedStream(new MemoryStream(b)))
        let md = blob.Metadata
        md.Add("ContextName", context.name)
        md.Add("Callback", callback)
        blob.SetMetadata()

        let qc = sa.CreateCloudQueueClient()
        let q = qc.GetQueueReference("import")
        let msg = new CloudQueueMessage(id)
        q.AddMessage(msg)

        (*let table = DataTable.New.Read(new IO.StreamReader(new IO.MemoryStream(buffer), Text.Encoding.UTF8))
        table.Rows |> Seq.iter (parseRow context)*)
            
        new EmptyResult()