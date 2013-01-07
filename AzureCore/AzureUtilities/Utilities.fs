#light

module ArtMaps.Azure.Utilities

module Cache =

    open Microsoft.ApplicationServer.Caching
    open Microsoft.ApplicationServer.Caching.AzureCommon

    let clearMetadata () =
        let cache = new DataCache("metadata")
        cache.Clear()

module Configuration = 
    
    open Microsoft.ApplicationServer.Caching
    open Microsoft.ApplicationServer.Caching.AzureCommon
    open Microsoft.WindowsAzure
    open Microsoft.WindowsAzure.Diagnostics
    open Microsoft.WindowsAzure.ServiceRuntime
    open Microsoft.WindowsAzure.StorageClient
    open System
    open System.Collections.Generic
    open System.IO
    open System.Linq

    type DevFabricTraceListener(file : string, name : string) =
        inherit System.Diagnostics.TraceListener(name)
        static let files = new Dictionary<string, StreamWriter>()
        do
            if (files.ContainsKey(file)) |> not then
                files.Add(file, new StreamWriter(new FileStream(file, FileMode.Append)))
        override this.Write(s : string) =
            let out = files.[file]
            out.Write(s)
            out.Flush()
        override this.WriteLine(s : string) =
            this.Write(s)
            this.Write(Environment.NewLine)
        override this.Finalize() =
            try
                if files.ContainsKey(file) then
                    files.[file].Close()
            with _ -> ()

    let inline value<'T> name : 'T =
        let v = RoleEnvironment.GetConfigurationSettingValue(name)
        let t = typeof<'T>
        System.Convert.ChangeType(v, t) :?> 'T

    let initSettings () =
        CloudStorageAccount.SetConfigurationSettingPublisher(
                fun configName configSetter ->
                        configSetter.Invoke(value configName) |> ignore
                        RoleEnvironment.Changed.Add(fun arg ->
                                if arg.Changes.OfType<RoleEnvironmentConfigurationSettingChange>()
                                        .Any(fun change -> change.ConfigurationSettingName = configName)
                                then
                                    if configSetter.Invoke(value configName) |> not
                                    then RoleEnvironment.RequestRecycle()
                        )
                )

    let initDiagnostics () =
        
        let diaConf = CacheDiagnostics.ConfigureDiagnostics(
                            DiagnosticMonitor.GetDefaultInitialConfiguration())
    
        let logLevel = Enum.Parse(typeof<LogLevel>, value("ArtMaps.Diagnostics.LogLevel"), true) :?> LogLevel
        let transferPeriod = TimeSpan.FromMinutes(value("ArtMaps.Diagnostics.TransferPeriod"))
        let bufferQuota = value("ArtMaps.Diagnostics.BufferQuota")

        diaConf.OverallQuotaInMB <- 20000

        diaConf.Logs.ScheduledTransferPeriod <- transferPeriod
        diaConf.Logs.BufferQuotaInMB <- bufferQuota
        diaConf.Logs.ScheduledTransferLogLevelFilter <- logLevel

        diaConf.Directories.ScheduledTransferPeriod <- transferPeriod
        diaConf.Directories.BufferQuotaInMB <- bufferQuota
        
        diaConf.DiagnosticInfrastructureLogs.ScheduledTransferPeriod <- transferPeriod
        diaConf.DiagnosticInfrastructureLogs.BufferQuotaInMB <- bufferQuota
        diaConf.DiagnosticInfrastructureLogs.ScheduledTransferLogLevelFilter <- logLevel

        diaConf.WindowsEventLog.ScheduledTransferPeriod <- transferPeriod
        diaConf.WindowsEventLog.BufferQuotaInMB <- bufferQuota
        diaConf.WindowsEventLog.ScheduledTransferLogLevelFilter <- logLevel
        
        DiagnosticMonitor.Start("Microsoft.WindowsAzure.Plugins.Diagnostics.ConnectionString", diaConf) |> ignore

    let initStorage () =
        let account = CloudStorageAccount.FromConfigurationSetting("ArtMaps.Storage.ConnectionString")
        
        let tables =
            match value<string>("ArtMaps.Storage.Tables") with
                | s when String.IsNullOrWhiteSpace(s) -> Array.empty
                | s -> s.Split(';')
        let tc = CloudStorageAccountStorageClientExtensions.CreateCloudTableClient(account)
        tables |> Array.iter (fun t -> tc.CreateTableIfNotExist(t) |> ignore)

        let containers =
            match value<string>("ArtMaps.Storage.Containers") with
                | s when String.IsNullOrWhiteSpace(s) -> Array.empty
                | s -> s.Split(';')
        let bc = CloudStorageAccountStorageClientExtensions.CreateCloudBlobClient(account)
        containers 
            |> Array.iter (
                fun c -> bc.GetContainerReference(c).CreateIfNotExist() |> ignore)
                
        let queues =
            match value<string>("ArtMaps.Storage.Queues") with
                | s when String.IsNullOrWhiteSpace(s) -> Array.empty
                | s -> s.Split(';')
        let qc = CloudStorageAccountStorageClientExtensions.CreateCloudQueueClient(account)
        queues |> Array.iter (fun q -> qc.GetQueueReference(q).CreateIfNotExist() |> ignore)


module Resources =

    open System
    open System.IO
    
    let approot = Path.Combine(Environment.GetEnvironmentVariable("RoleRoot") + @"\", "approot")
      
    let readOnly name =
        let path = Path.Combine(approot + @"\", name)
        new FileStream(path, FileMode.Open, FileAccess.Read) :> Stream

    let MasterKey =
        try
            use i = readOnly @"Keys\MasterKey.blob"
            use o = new MemoryStream()
            i.CopyTo(o)
            o.GetBuffer()
        with _ as e ->
            System.Diagnostics.Trace.TraceError(sprintf "Unable to open the master key file: %s\n%s" e.Message e.StackTrace)
            raise e
