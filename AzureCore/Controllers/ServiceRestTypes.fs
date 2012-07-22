﻿#light

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
type QueryParameters = {
    boundingBox : BoundingBox;    
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
