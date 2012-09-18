#light

module ArtMaps.Controllers.Types

open System.Web.Http.ModelBinding

module WU = ArtMaps.Utilities.Web

[<ModelBinder(typeof<WU.RecordBinderProvider>)>]
type Point = {
    latitude : int64
    longitude : int64
}

[<ModelBinder(typeof<WU.RecordBinderProvider>)>]
type BoundingBox = {
    northEast : Point
    southWest : Point
}

[<ModelBinder(typeof<WU.RecordBinderProvider>)>]
type OoIQueryParameters = {
    boundingBox : BoundingBox
}

[<ModelBinder(typeof<WU.RecordBinderProvider>)>]
type UserQueryParameters = {
    URI : string
}

type Action = {
    ID : int64
    URI : string
    userID : int64
    datetime : int64
    username : string
    userLevel : string
    timestamp : int64
    signature : string
}

type PointLocation = {
    ID : int64
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
    ID : int64
    URI : string
    locations : PointLocation seq
    actions : Action seq
    username : string
    userLevel : string
    timestamp : int64
    signature : string
}

type User = {
    ID : int64
    URI : string
}

    
    module JsonConverters =
    
        type ActionConverter() =
            inherit Newtonsoft.Json.JsonConverter()
    
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

                writer.WritePropertyName("userID")
                writer.WriteValue(a.userID)

                writer.WritePropertyName("datetime")
                writer.WriteValue(a.datetime)
                                
                writer.WriteEndObject()

        type PointLocationConverter() =
            inherit Newtonsoft.Json.JsonConverter()
    
            override this.CanRead with get () = false
            override this.CanWrite with get () = true
            override this.CanConvert(t) = t = typeof<PointLocation>

            override this.ReadJson(reader, t, ob, serializer) =
                raise (new System.NotImplementedException())

            override this.WriteJson(writer, ob, serializer) = 

                let p = ob :?> PointLocation
                writer.WriteStartObject()
                
                writer.WritePropertyName("ID")
                writer.WriteValue(p.ID)

                writer.WritePropertyName("source")
                writer.WriteValue(p.source)

                writer.WritePropertyName("latitude")
                writer.WriteValue(p.latitude)

                writer.WritePropertyName("longitude")
                writer.WriteValue(p.longitude)

                writer.WritePropertyName("error")
                writer.WriteValue(p.error)
                                
                writer.WriteEndObject()


        type ObjectOfInterestConverter() =
            inherit Newtonsoft.Json.JsonConverter()

            static let acConv = new ActionConverter()
            static let plConv = new PointLocationConverter()
    
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

                writer.WritePropertyName("locations")
                writer.WriteStartArray()
                o.locations |> Seq.iter (fun l -> plConv.WriteJson(writer, l, serializer))
                writer.WriteEndArray()

                writer.WritePropertyName("actions")
                writer.WriteStartArray()
                o.actions |> Seq.iter (fun a -> acConv.WriteJson(writer, a, serializer))
                writer.WriteEndArray()
                
                writer.WriteEndObject()

        type UserConverter() =
            inherit Newtonsoft.Json.JsonConverter()
    
            override this.CanRead with get () = false
            override this.CanWrite with get () = true
            override this.CanConvert(t) = t = typeof<User>

            override this.ReadJson(reader, t, ob, serializer) =
                raise (new System.NotImplementedException())

            override this.WriteJson(writer, ob, serializer) = 

                let a = ob :?> User
                writer.WriteStartObject()
                
                writer.WritePropertyName("ID")
                writer.WriteValue(a.ID)

                writer.WritePropertyName("URI")
                writer.WriteValue(a.URI)
                                
                writer.WriteEndObject()
                

    module Conversions =

        open ArtMaps.Persistence
        open Microsoft.SqlServer.Types
        open System
        open System.Data.SqlTypes
        open System.IO
        open System.Xml
        open System.Xml.Linq
        
        let SRID = 4326

        let Epoch = new System.DateTime(1970, 1, 1, 0, 0, 0, System.DateTimeKind.Utc)

        let CoordConvFactor = 10.0**8.0
        
        let ToIntCoord (f : float) = int64 (f * CoordConvFactor)
        
        let ToFloatCoord (i : int64) = (float i) / CoordConvFactor

        let ToDateTime (i : int64) =
            Epoch + System.TimeSpan.FromMilliseconds(float i)

        let ToTimeStamp (d : System.DateTime) =
            int64 (d - Epoch).TotalMilliseconds

        let ActionToActionRecord (a : Entities.Action) =
            {
                Action.ID = a.ID
                URI = a.URI
                userID = a.UserID
                datetime = ToTimeStamp a.DateTime
                username = null
                userLevel = null
                timestamp = 0L
                signature = null
            }

        let LocationToLocationRecord (l : Entities.Location) =
            match l.LocationType with
                | Entities.LocationType.Point -> 
                    let lp = l.LocationPoint
                    let g = lp.Center
                    Some({
                            PointLocation.ID = l.ID
                            source = System.Enum.GetName(typeof<Entities.LocationSource>, l.Source)
                            latitude = ToIntCoord g.Lat.Value
                            longitude = ToIntCoord g.Long.Value
                            error = lp.Error
                            username = null
                            userLevel = null
                            timestamp = 0L
                            signature = null
                    })
                | _ -> None

        let ObjectToObjectRecord (o : Entities.ObjectOfInterest) =
            {
                ObjectOfInterest.ID = o.ID
                URI = o.URI
                locations = o.Locations |> Seq.choose LocationToLocationRecord
                actions = o.Actions |> Seq.map ActionToActionRecord
                username = null
                userLevel = null
                timestamp = 0L
                signature = null
            }

        let XmlToLocationRecords(xml : string) =
            let doc = XDocument.Load(new StringReader(xml))
            query {
                for loc in doc.Descendants(XName.op_Implicit("Location")) do select loc
            }
            |> Seq.map (
                fun l -> 
                let center = SqlGeography.Parse(new SqlString(l.Element(XName.op_Implicit("CenterText")).Value))
                {
                    PointLocation.ID = Convert.ToInt64(l.Element(XName.op_Implicit("ID")).Value)
                    source = System.Enum.GetName(typeof<Entities.LocationSource>, Convert.ToInt16(l.Element(XName.op_Implicit("Source")).Value))
                    latitude = ToIntCoord center.Lat.Value
                    longitude = ToIntCoord center.Long.Value
                    error = Convert.ToInt64(l.Element(XName.op_Implicit("Error")).Value)
                    username = null
                    userLevel = null
                    timestamp = 0L
                    signature = null
                })
            |> Seq.toList

        let XmlToActionRecords(xml : string) =
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
            |> Seq.toList

        let InBoundsResultToObjectRecord (r : Entities.SelectObjectsWithinBoundsResult) =
            {
                ObjectOfInterest.ID = r.ID
                URI = r.URI
                locations = match r.Locations with | null -> [] | _ -> r.Locations |> XmlToLocationRecords
                actions = match r.Actions with | null -> [] | _ -> r.Actions |> XmlToActionRecords
                username = null
                userLevel = null
                timestamp = 0L
                signature = null
            }

        let UserToUserRecord (u : Entities.User) =
            {
                User.ID = u.ID
                URI = u.URI
            }
