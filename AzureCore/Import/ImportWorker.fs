#light

namespace ArtMaps.Import

open Microsoft.WindowsAzure
open Microsoft.WindowsAzure.ServiceRuntime
open Microsoft.WindowsAzure.StorageClient
open System
open System.Net
open System.Threading

module C = ArtMaps.Azure.Utilities.Configuration
module Log = ArtMaps.Utilities.Log
module IU = ArtMaps.Import.Utilities

type ImportWorker() =
    inherit RoleEntryPoint()    

    override wr.Run() =

        let cs : string = C.value("ArtMaps.Storage.ConnectionString")
        let sa = 
            if cs.ToLower().Contains("usedevelopmentstorage=true") then
                CloudStorageAccount.DevelopmentStorageAccount
            else
                CloudStorageAccount.Parse(cs)

        try
            let current = IU.init sa
            while true do 
                match current.queue.GetMessage() with
                    | null -> Thread.Sleep(TimeSpan.FromMinutes(1.0))
                    | msg -> IU.import { current with item = Some(msg) }
        with
            | :? IU.SetupNotCompleteException as e -> 
                sprintf "A SetupNotCompleteException occurred, ImportWorker thread will now exit\n%s\n%s" 
                        e.Message e.StackTrace |> Log.error
            | e ->
                sprintf "An unknown exception occurred, ImportWorker thread will now exit\n%s\n%s" 
                        e.Message e.StackTrace |> Log.error

            
    override wr.OnStart() = 

        ServicePointManager.DefaultConnectionLimit <- 12       
        ArtMaps.Azure.Utilities.Configuration.initSettings();
        ArtMaps.Azure.Utilities.Configuration.initDiagnostics();
        ArtMaps.Azure.Utilities.Configuration.initStorage();

        base.OnStart()
