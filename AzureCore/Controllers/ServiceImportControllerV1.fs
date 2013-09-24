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

    [<HttpPost>]
    member this.Import 
        ([<ModelBinder(typeof<WU.MvcContextBinder>)>]context : CTX.t,
            signature: string,
            callback: string,
            file : HttpPostedFileBase) = 

        let ms = new MemoryStream(file.ContentLength)
        file.InputStream.CopyTo(ms)

        let b = ms.GetBuffer()
        
        if context.verifySignature b (signature :> obj) |> not then
            raise (HttpException(int Net.HttpStatusCode.Forbidden, Convert.ToString(Er.ForbiddenMinorCode.InvalidSignature)))

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
        blob.UploadFromStream(new MemoryStream(b))
        let md = blob.Metadata
        md.Add("ContextName", context.name)
        md.Add("Callback", callback)
        blob.SetMetadata()

        let qc = sa.CreateCloudQueueClient()
        let q = qc.GetQueueReference("import")
        let msg = new CloudQueueMessage(id)
        q.AddMessage(msg)
            
        new EmptyResult()