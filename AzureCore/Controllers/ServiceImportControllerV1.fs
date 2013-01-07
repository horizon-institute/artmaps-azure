#light 

namespace ArtMaps.Controllers

open ArtMaps.Persistence.Context
open ArtMaps.Persistence.Entities
open DataAccess
open Microsoft.SqlServer.Types
open System
open System.Linq
open System.Web
open System.Web.Mvc

module Er = Errors
module CTX = ArtMaps.Context
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
            file : HttpPostedFileBase) = 
        
        let buffer = Array.create file.ContentLength (byte 0)
        file.InputStream.Read(buffer, 0, file.ContentLength) |> ignore
        if context.verifySignature buffer (signature :> obj) |> not then
            raise (Er.Forbidden(Er.ForbiddenMinorCode.InvalidSignature))

        let table = DataTable.New.Read(new IO.StreamReader(new IO.MemoryStream(buffer), Text.Encoding.UTF8))
        table.Rows |> Seq.iter (parseRow context)
            
        new EmptyResult()