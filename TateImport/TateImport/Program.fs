#light

open CsvHelper
open System
open System.Collections.Generic
open System.Data
open System.Data.SqlClient
open System.IO

let CONNS = "Server=tcp:m691s0bl8f.database.windows.net,1433;Database=artmapsdev;User ID=dominicjprice@m691s0bl8f;Password=tyPhuadJit9;Trusted_Connection=False;Encrypt=True;Connection Timeout=30"
let FEDC = "USE FEDERATION ContextFederation(ContextID=0) WITH RESET, FILTERING=OFF"

let COLLECTIONS = @"C:\Users\pszdp1\Desktop\collection.csv"
let PLACES = @"C:\Users\pszdp1\Desktop\latlongplace.csv"
let LOCS = @"C:\Users\pszdp1\Desktop\collection_latlong.csv"

let collectionMap =
    let map = new Dictionary<int64, string>()
    use r = new CsvReader(new StreamReader(COLLECTIONS))
    while r.Read() do
        let ID = r.GetField<int64>(0)
        let acno = r.GetField(2).Trim()
        let URI = sprintf "tatecollection://%s" acno
        map.Add(ID, URI)
    map

let placeMap =
    let map = new Dictionary<int64, float * float>()
    use r = new CsvReader(new StreamReader(PLACES))
    while r.Read() do
        let ID = r.GetField<int64>("LATLONGPLACE_ID")
        let lat = r.GetField<float>("LATITUDE")
        let lon = r.GetField<float>("LONGITUDE")
        map.Add(ID, (lat, lon))
    map

(*let collections (conn : SqlConnection) = 
    let r = new CsvReader(new StreamReader(COLLECTIONS))
    let mutable ID = 0
    while r.Read() do
        let acno = r.GetField(2).Trim()
        let URI = sprintf "tatecollection://%s" acno
        printfn "%s" URI
        let c = sprintf "INSERT INTO [ObjectOfInterest] (ID, ContextID, URI) VALUES (%i, 0, '%s')" ID URI
        let cmd = new SqlCommand(c, conn)
        cmd.ExecuteNonQuery() |> ignore
        ID <- ID + 1
    let cmd = new SqlCommand(sprintf "UPDATE [Sequence] SET CurrentID = %i WHERE TableName = 'ObjectOfInterest'" ID, conn)
    cmd.ExecuteNonQuery() |> ignore*)

[<EntryPoint>]
let main argv = 
    //collections conn
    (*use r = new CsvReader(new StreamReader(LOCS))
    seq { 
        while r.Read() do 
            let urif = r.GetField<int64>("COLLECTION_ID")
            let locf = r.GetField<int64>("LATLONGPLACE_ID")
            if collectionMap.ContainsKey(urif) && placeMap.ContainsKey(locf) then
                let uri = collectionMap.[urif]
                let loc = placeMap.[locf]
                yield (uri, loc)
    }
    |> Seq.mapi (fun id (uri, (lat, lon)) ->
                    async {
                        try
                            let conn = new SqlConnection(CONNS)
                            conn.Open()
                            let initc = new SqlCommand(FEDC, conn)
                            initc.ExecuteNonQuery() |> ignore
                            let oidc = new SqlCommand(sprintf "SELECT ID FROM [ObjectOfInterest] WHERE URI = '%s'" uri, conn)
                            let oid = Convert.ToInt64(oidc.ExecuteScalar())
                            printfn "%i: %f, %f" oid lat lon
                            let lc = new SqlCommand(sprintf "INSERT INTO [Location] (ID, ContextID, Source, ObjectID) VALUES (%i, 0, 0, %i)" id oid, conn)
                            lc.ExecuteNonQuery() |> ignore
                            let lpc = new SqlCommand(sprintf "INSERT INTO [LocationPoint] (ID, ContextID, Error, LocationID, CenterText) VALUES (%i, 0, 0, %i, 'POINT(%f %f)')" id id lon lat, conn)
                            lpc.ExecuteNonQuery() |> ignore
                            conn.Close()
                        with _ as e ->
                            printfn "%s\n%s" e.Message e.StackTrace
                    }
                )
    |> Async.Parallel |> Async.RunSynchronously |> ignore*)
    
    let conn = new SqlConnection(CONNS)
    conn.Open()
    let initc = new SqlCommand(FEDC, conn)
    initc.ExecuteNonQuery() |> ignore
    let cmd = new SqlCommand("SELECT ObjectID FROM [Location] GROUP BY ObjectID HAVING COUNT(ID) > 1", conn)
    let r = cmd.ExecuteReader()
    let oids = seq {
                    while r.Read() do
                        yield r.GetInt64(0)
                } |> Seq.toList
    r.Close()
    conn.Close()
    
    oids |> Seq.ofList |> Seq.map (
        fun oid -> 
            async {
                let conn = new SqlConnection(CONNS)
                conn.Open()
                let initc = new SqlCommand(FEDC, conn)
                initc.ExecuteNonQuery() |> ignore
                let cmd = new SqlCommand(sprintf "SELECT ID FROM [Location] WHERE ObjectID = %i" oid, conn)
                let r = cmd.ExecuteReader()
                r.Read() |> ignore
                let td = seq {
                                while r.Read() do
                                    yield r.GetInt64(0)
                                r.Close()
                            } |> Seq.toList
                r.Close()
                td 
                |> Seq.ofList 
                |> Seq.iter (
                    fun lid ->
                        let cmd = new SqlCommand(sprintf "DELETE FROM [LocationPoint] WHERE LocationID = %i" lid, conn)
                        printfn "%i" (cmd.ExecuteNonQuery())
                        let cmd = new SqlCommand(sprintf "DELETE FROM [Location] WHERE ID = %i" lid, conn)
                        printfn "%i" (cmd.ExecuteNonQuery())
                    )
                conn.Close()
            }
        )
    |> Async.Parallel |> Async.RunSynchronously |> ignore
    
    0 
