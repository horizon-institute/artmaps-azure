#light

module ArtMaps.Controllers.Types.V2

type SRIDS = 
    | WGS84 = 4326 // Coordinate system used in GPS

let CoordConvFactor = 10.0**8.0

let CoordAsInt (f : float) = int64 (f * CoordConvFactor)

let CoordAsFloat (i : int64) = (float i) / CoordConvFactor

let Epoch = new System.DateTime(1970, 1, 1, 0, 0, 0, System.DateTimeKind.Utc)


module Out =

    module Metadata = ArtMaps.Controllers.Metadata

    type MetadataUnion =
        | URI of string
        | Object of Metadata.t

    type User = {
        ID : int64
        URI : string
        actions : ActionUnion list
        pingbacks : PingbackUnion list
    }

    and UserUnion =
        | URI of string
        | Object of User

    and Pingback = {
        ID : int64
        URL : string
        datetime : int64
        users : UserUnion list
        objects : ObjectOfInterestUnion list
        actions : ActionUnion list
        locations : PointLocationUnion list
    }

    and PingbackUnion =
        | URI of string
        | Object of Pingback

    and Action = {
        ID : int64
        URI : string
        user : UserUnion
        datetime : int64
        locations : PointLocationUnion list
        pingbacks : PingbackUnion list
    }

    and ActionUnion =
        | URI of string
        | Object of Action

    and PointLocation = {
        ID : int64
        source : string
        latitude : int64
        longitude : int64
        error : int64
        actions : ActionUnion list
        pingbacks : PingbackUnion list
    }

    and PointLocationUnion =
        | URI of string
        | Object of PointLocation

    and ObjectOfInterest = {
        ID : int64
        URI : string
        locations : PointLocationUnion list
        actions : ActionUnion list
        pingbacks : PingbackUnion list
        metadata : MetadataUnion list
    }

    and ObjectOfInterestUnion =
        | URI of string
        | Object of ObjectOfInterest


    module Conversions =

        open ArtMaps.Persistence
        open Microsoft.SqlServer.Types
        open System.Data.SqlTypes
        open System.IO
        open System.Xml.Linq

        module Util = ArtMaps.Utilities.General

        let timestamp (d : System.DateTime) =
            int64 (d - Epoch).TotalMilliseconds

        let metadata (depth : int32) (omd : Entities.ObjectMetadata) =
            match depth with 
                | 0 -> MetadataUnion.URI(sprintf "/metadata/%i" omd.ID)
                | _ ->
                    MetadataUnion.Object(
                        {
                            Metadata.t.ID = omd.ID
                            Metadata.t.name = omd.Name
                            Metadata.t.value = omd.Value
                            Metadata.t.valueType = Util.enumName omd.ValueType
                        })

        let rec user (depth : int32) (u : Entities.User) =
            match depth with
                | 0 -> UserUnion.URI(sprintf "/users/%i" u.ID)
                | _ -> 
                    let ndepth = depth - 1
                    UserUnion.Object(
                        {
                            User.ID = u.ID
                            URI = u.URI
                            actions = u.Actions |> Seq.toList |> List.map (action ndepth)
                            pingbacks = u.PingbackUsers |> Seq.toList |> List.map (fun pu -> pingback ndepth pu.Pingback)
                        })

        and pingback (depth : int32) (p : Entities.Pingback) =
            match depth with
                | 0 -> PingbackUnion.URI(sprintf "/pingbacks/%i" p.ID)
                | _ -> 
                    let ndepth = depth - 1
                    PingbackUnion.Object(
                        {
                            Pingback.ID = p.ID
                            URL = p.URL
                            datetime = timestamp p.DateTime
                            users = p.PingbackUsers |> Seq.toList |> List.map (fun pu -> user ndepth pu.User)
                            objects = p.PingbackObjects |> Seq.toList |> List.map (fun po -> objectOfInterest ndepth po.ObjectOfInterest)
                            actions = p.PingbackActions |> Seq.toList |> List.map (fun pa -> action ndepth pa.Action)
                            locations = p.PingbackLocations |> Seq.toList |> List.map (fun pl -> location ndepth pl.Location)
                        })                            

        and action (depth : int32) (a : Entities.Action) =
            match depth with
                | 0 -> ActionUnion.URI(sprintf "/actions/%i" a.ID)
                | _ -> 
                    let ndepth = depth - 1
                    ActionUnion.Object(
                        {
                            Action.ID = a.ID
                            URI = a.URI
                            user = user 0 a.User
                            datetime = timestamp a.DateTime
                            locations = a.ActionLocations |> Seq.toList |> List.map (fun al -> location ndepth al.Location)
                            pingbacks = a.PingbackActions |> Seq.toList |> List.map (fun pa -> pingback ndepth pa.Pingback)
                        })

        and location (depth : int32) (l : Entities.Location) =
            match depth with
                | 0 -> PointLocationUnion.URI(sprintf "/locations/%i" l.ID)
                | _ -> 
                    let lp = l.LocationPoint
                    let c = lp.Center
                    let ndepth = depth - 1
                    PointLocationUnion.Object(
                        {
                            PointLocation.ID = l.ID
                            source = System.Enum.GetName(typeof<Entities.LocationSource>, l.Source)
                            latitude = CoordAsInt c.Lat.Value
                            longitude = CoordAsInt c.Long.Value
                            error = lp.Error
                            actions = l.ActionLocations |> Seq.toList |> List.map (fun al -> action ndepth al.Action)
                            pingbacks = l.PingbackLocations |> Seq.toList |> List.map (fun pl -> pingback ndepth pl.Pingback)
                        })
        
        and objectOfInterest (depth : int32) (o : Entities.ObjectOfInterest) =
            match depth with
                | 0 -> ObjectOfInterestUnion.URI(sprintf "/objectsofinterest/%i" o.ID)
                | _ -> 
                    let ndepth = depth - 1
                    ObjectOfInterestUnion.Object(
                        {
                            ObjectOfInterest.ID = o.ID
                            URI = o.URI
                            locations = o.Locations |> Seq.toList |> List.map (location ndepth)
                            actions = o.Actions |> Seq.toList |> List.map (action ndepth)
                            pingbacks = o.PingbackObjects |> Seq.toList |> List.map (fun po -> pingback ndepth po.Pingback)
                            metadata = o.ObjectMetadatas |> Seq.toList |> List.map (metadata ndepth)
                        })

        ///////////////

        let fromLocationXML (depth : int32) (xml : string) =
            let doc = XDocument.Load(new StringReader(xml))
            let q = query {
                for loc in doc.Descendants(XName.op_Implicit("Location")) do select loc
            }
            match depth with
                | 0 -> 
                    q |> Seq.map (
                        fun l -> 
                                let id = l.Element(XName.op_Implicit("ID")).Value
                                PointLocationUnion.URI(sprintf "/locations/%s" id)) 
                        |> Seq.toList
                | 1 -> 
                    q |> Seq.map (
                        fun l -> 
                                let center = SqlGeography.Parse(new SqlString(l.Element(XName.op_Implicit("CenterText")).Value))
                                PointLocationUnion.Object(
                                    {
                                        PointLocation.ID = System.Convert.ToInt64(l.Element(XName.op_Implicit("ID")).Value)
                                        source = System.Enum.GetName(typeof<Entities.LocationSource>, System.Convert.ToInt16(l.Element(XName.op_Implicit("Source")).Value))
                                        latitude = CoordAsInt center.Lat.Value
                                        longitude = CoordAsInt center.Long.Value
                                        error = System.Convert.ToInt64(l.Element(XName.op_Implicit("Error")).Value)
                                        actions = []
                                        pingbacks = []
                                    }))
                        |> Seq.toList
                | _ -> raise (new System.NotImplementedException())

        
        (*let XmlToActionRecords(xml : string) =
            let doc = XDocument.Load(new StringReader(xml))
            query {
                for ac in doc.Descendants(XName.op_Implicit("Action")) do select ac
            }
            |> Seq.map (
                fun a -> 
                {
                    Action.ID = Convert.ToInt64(a.Element(XName.op_Implicit("ID")).Value)
                    URI = a.Element(XName.op_Implicit("URI")).Value
                    userID = Convert.ToInt64(a.Element(XName.op_Implicit("UserID")).Value)
                    datetime = ToTimeStamp (DateTime.Parse(a.Element(XName.op_Implicit("DateTime")).Value))
                    username = null
                    userLevel = null
                    timestamp = 0L
                    signature = null
                })
            |> Seq.toList*)

        (*let inBoundsResult (depth : int32) (r : Entities.SelectObjectsWithinBoundsResult) =
            let ndepth = depth - 1
            {
                ObjectOfInterest.ID = r.ID
                URI = r.URI
                locations = match r.Locations with | null -> [] | _ -> r.Locations |> fromLocationXML ndepth
                actions = match r.Actions with | null -> [] | _ -> r.Actions |> XmlToActionRecords
                pingbacks = []
                metadata = []
            }*)

        ////////////////


        module JsonConverters = 

            type MetadataConverter() =
                inherit Newtonsoft.Json.JsonConverter()

                override this.CanRead with get () = false
                override this.CanWrite with get () = true
                override this.CanConvert(t) = t = typeof<Metadata.t>

                override this.ReadJson(reader, t, ob, serializer) =
                    raise (new System.NotImplementedException())

                override this.WriteJson(writer, ob, serializer) = 

                    let md = ob :?> Metadata.t
                    writer.WriteStartObject()

                    writer.WritePropertyName("ID")
                    writer.WriteValue(md.ID)

                    writer.WritePropertyName("name")
                    writer.WriteValue(md.name)

                    writer.WritePropertyName("value")
                    writer.WriteValue(md.value)

                    writer.WritePropertyName("valueType")
                    writer.WriteValue(md.valueType)

                    writer.WriteEndObject()

            type UserConverter() =
                inherit Newtonsoft.Json.JsonConverter()

                static let aConv = new ActionConverter()
                static let pConv = new PingbackConverter()
    
                override this.CanRead with get () = false
                override this.CanWrite with get () = true
                override this.CanConvert(t) = t = typeof<User>

                override this.ReadJson(reader, t, ob, serializer) =
                    raise (new System.NotImplementedException())

                override this.WriteJson(writer, ob, serializer) = 

                    let u = ob :?> User
                    writer.WriteStartObject()

                    writer.WritePropertyName("ID")
                    writer.WriteValue(u.ID)

                    writer.WritePropertyName("URI")
                    writer.WriteValue(u.URI)

                    writer.WritePropertyName("actions")
                    writer.WriteStartArray()
                    u.actions |> Seq.iter (
                        fun a -> 
                            match a with 
                                | ActionUnion.URI v -> writer.WriteValue(v)
                                | ActionUnion.Object v -> aConv.WriteJson(writer, v, serializer))
                    writer.WriteEndArray()

                    writer.WritePropertyName("pingbacks")
                    writer.WriteStartArray()
                    u.pingbacks |> Seq.iter (
                        fun p ->
                            match p with
                                | PingbackUnion.URI v -> writer.WriteValue(v)
                                | PingbackUnion.Object v -> pConv.WriteJson(writer, v, serializer))
                    writer.WriteEndArray()
                                                
                    writer.WriteEndObject()

            and PingbackConverter() =
                inherit Newtonsoft.Json.JsonConverter()

                static let uConv = new UserConverter()
                static let oConv = new ObjectOfInterestConverter()
                static let aConv = new ActionConverter()
                static let lConv = new LocationConverter()
    
                override this.CanRead with get () = false
                override this.CanWrite with get () = true
                override this.CanConvert(t) = t = typeof<Pingback>

                override this.ReadJson(reader, t, ob, serializer) =
                    raise (new System.NotImplementedException())

                override this.WriteJson(writer, ob, serializer) = 

                    let p = ob :?> Pingback
                    writer.WriteStartObject()

                    writer.WritePropertyName("ID")
                    writer.WriteValue(p.ID)

                    writer.WritePropertyName("URL")
                    writer.WriteValue(p.URL)

                    writer.WritePropertyName("datetime")
                    writer.WriteValue(p.datetime)

                    writer.WritePropertyName("users")
                    writer.WriteStartArray()
                    p.users |> Seq.iter (
                        fun u ->
                            match u with
                                | UserUnion.URI v -> writer.WriteValue(v)
                                | UserUnion.Object v -> uConv.WriteJson(writer, v, serializer))
                    writer.WriteEndArray()

                    writer.WritePropertyName("objects")
                    writer.WriteStartArray()
                    p.objects |> Seq.iter (
                        fun o ->
                            match o with
                                | ObjectOfInterestUnion.URI v -> writer.WriteValue(v)
                                | ObjectOfInterestUnion.Object v -> oConv.WriteJson(writer, v, serializer))
                    writer.WriteEndArray()

                    writer.WritePropertyName("actions")
                    writer.WriteStartArray()
                    p.actions |> Seq.iter (
                        fun a ->
                            match a with
                                | ActionUnion.URI v -> writer.WriteValue(v)
                                | ActionUnion.Object v -> aConv.WriteJson(writer, v, serializer))
                    writer.WriteEndArray()

                    writer.WritePropertyName("locations")
                    writer.WriteStartArray()
                    p.locations |> Seq.iter (
                        fun l ->
                            match l with
                                | PointLocationUnion.URI v -> writer.WriteValue(v)
                                | PointLocationUnion.Object v -> lConv.WriteJson(writer, v, serializer))
                    writer.WriteEndArray()
                                                
                    writer.WriteEndObject()

            and ActionConverter() =
                inherit Newtonsoft.Json.JsonConverter()

                static let uConv = new UserConverter()
                static let lConv = new LocationConverter()
                static let pConv = new PingbackConverter()
    
                override this.CanRead with get () = false
                override this.CanWrite with get () = true
                override this.CanConvert(t) = t = typeof<Action>

                override this.ReadJson(reader, t, ob, serializer) =
                    raise (new System.NotImplementedException())

                override this.WriteJson(writer, ob, serializer) = 

                    let a = ob :?> Action
                    writer.WriteStartObject()

                    writer.WritePropertyName("ID")
                    writer.WriteValue(a.ID)

                    writer.WritePropertyName("URI")
                    writer.WriteValue(a.URI)

                    writer.WritePropertyName("user")
                    match a.user with
                        | UserUnion.URI v -> writer.WriteValue(v)
                        | UserUnion.Object v -> uConv.WriteJson(writer, v, serializer)

                    writer.WritePropertyName("datetime")
                    writer.WriteValue(a.datetime)

                    writer.WritePropertyName("locations")
                    writer.WriteStartArray()
                    a.locations |> Seq.iter (
                        fun l ->
                            match l with
                                | PointLocationUnion.URI v -> writer.WriteValue(v)
                                | PointLocationUnion.Object v -> lConv.WriteJson(writer, v, serializer))
                    writer.WriteEndArray()

                    writer.WritePropertyName("pingbacks")
                    writer.WriteStartArray()
                    a.pingbacks |> Seq.iter (
                        fun p ->
                            match p with
                                | PingbackUnion.URI v -> writer.WriteValue(v)
                                | PingbackUnion.Object v -> pConv.WriteJson(writer, v, serializer))
                    writer.WriteEndArray()
                                                
                    writer.WriteEndObject()

            and LocationConverter() =
                inherit Newtonsoft.Json.JsonConverter()
    
                static let aConv = new ActionConverter()
                static let pConv = new PingbackConverter()

                override this.CanRead with get () = false
                override this.CanWrite with get () = true
                override this.CanConvert(t) = t = typeof<PointLocation>

                override this.ReadJson(reader, t, ob, serializer) =
                    raise (new System.NotImplementedException())

                override this.WriteJson(writer, ob, serializer) = 

                    let l = ob :?> PointLocation
                    writer.WriteStartObject()

                    writer.WritePropertyName("ID")
                    writer.WriteValue(l.ID)

                    writer.WritePropertyName("source")
                    writer.WriteValue(l.source)

                    writer.WritePropertyName("latitude")
                    writer.WriteValue(l.latitude)

                    writer.WritePropertyName("longitude")
                    writer.WriteValue(l.longitude)

                    writer.WritePropertyName("error")
                    writer.WriteValue(l.error)

                    writer.WritePropertyName("actions")
                    writer.WriteStartArray()
                    l.actions |> Seq.iter (
                        fun a ->
                            match a with
                                | ActionUnion.URI v -> writer.WriteValue(v)
                                | ActionUnion.Object v -> aConv.WriteJson(writer, v, serializer))
                    writer.WriteEndArray()

                    writer.WritePropertyName("pingbacks")
                    writer.WriteStartArray()
                    l.pingbacks |> Seq.iter (
                        fun p ->
                            match p with
                                | PingbackUnion.URI v -> writer.WriteValue(v)
                                | PingbackUnion.Object v -> pConv.WriteJson(writer, v, serializer))
                    writer.WriteEndArray()
                                                
                    writer.WriteEndObject()

            and ObjectOfInterestConverter() =
                inherit Newtonsoft.Json.JsonConverter()

                static let aConv = new ActionConverter()
                static let lConv = new LocationConverter()
                static let pConv = new PingbackConverter()
                static let mdConv = new MetadataConverter()
    
                override this.CanRead with get () = false
                override this.CanWrite with get () = true
                override this.CanConvert(t) = t = typeof<ObjectOfInterest>

                override this.ReadJson(reader, t, ob, serializer) =
                    raise (new System.NotImplementedException())

                override this.WriteJson(writer, ob, serializer) = 

                    let o = ob :?> ObjectOfInterest
                    writer.WriteStartObject()

                    writer.WritePropertyName("ID")
                    writer.WriteValue(o.ID)

                    writer.WritePropertyName("URI")
                    writer.WriteValue(o.URI)

                    writer.WritePropertyName("actions")
                    writer.WriteStartArray()
                    o.actions |> Seq.iter (
                        fun a ->
                            match a with
                                | ActionUnion.URI v -> writer.WriteValue(v)
                                | ActionUnion.Object v -> aConv.WriteJson(writer, v, serializer))
                    writer.WriteEndArray()

                    writer.WritePropertyName("locations")
                    writer.WriteStartArray()
                    o.locations |> Seq.iter (
                        fun l ->
                            match l with
                                | PointLocationUnion.URI v -> writer.WriteValue(v)
                                | PointLocationUnion.Object v -> lConv.WriteJson(writer, v, serializer))
                    writer.WriteEndArray()

                    writer.WritePropertyName("pingbacks")
                    writer.WriteStartArray()
                    o.pingbacks |> Seq.iter (
                        fun p ->
                            match p with
                                | PingbackUnion.URI v -> writer.WriteValue(v)
                                | PingbackUnion.Object v -> pConv.WriteJson(writer, v, serializer))
                    writer.WriteEndArray()

                    writer.WritePropertyName("metadata")
                    writer.WriteStartArray()
                    o.metadata |> Seq.iter (
                        fun md ->
                            match md with
                                | MetadataUnion.URI v -> writer.WriteValue(v)
                                | MetadataUnion.Object v -> mdConv.WriteJson(writer, v, serializer))
                    writer.WriteEndArray()
                                                
                    writer.WriteEndObject()


module In =

    module Conversions =

        let datetime (i : int64) =
            Epoch + System.TimeSpan.FromMilliseconds(float i)

    type Metadata = {
        name : string
        value : string
        valueType: string
        username : string
        userLevel : string
        timestamp : int64
        signature : string
    }

    type Pingback = {
        URL : string
        datetime : int64
        username : string
        userLevel : string
        timestamp : int64
        signature : string
    }

    type Action = {
        URI : string
        datetime : int64
        username : string
        userLevel : string
        timestamp : int64
        signature : string
    }   

    type PointLocation = {
        source : string
        latitude : int64
        longitude : int64
        error : int64
        username : string
        userLevel : string
        timestamp : int64
        signature : string
    }

    type ObjectOfInterest = {
        URI : string
        username : string
        userLevel : string
        timestamp : int64
        signature : string
    }

    type LocationSearch = {
        latitudeNorth : int64
        latitudeSouth : int64
        longitudeEast : int64
        longitudeWest : int64
    }