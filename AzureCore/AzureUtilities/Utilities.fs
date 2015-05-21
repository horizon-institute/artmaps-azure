#light

module ArtMaps.Azure.Utilities

module Configuration = 
    
    open Microsoft.Azure
    open Microsoft.WindowsAzure.Storage
    open System

    let inline value<'T> name : 'T =
        let v = CloudConfigurationManager.GetSetting(name)
        let t = typeof<'T>
        Convert.ChangeType(v, t) :?> 'T

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
