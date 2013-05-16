#light

namespace ArtMaps.Controllers

open ArtMaps.Persistence.Entities
open Microsoft.SqlServer.Types
open Newtonsoft.Json
open Newtonsoft.Json.Linq
open System.Linq
open System.Text
open System.Text.RegularExpressions
open System.Web.Http
open System.Web.Http.ModelBinding

module CTX = ArtMaps.Context
module Err = Errors
module In = ArtMaps.Controllers.Types.V2.In.Conversions
module Log = ArtMaps.Utilities.Log
module Out = ArtMaps.Controllers.Types.V2.Out.Conversions
module Types = ArtMaps.Controllers.Types.V2
module WU = ArtMaps.Utilities.Web

    module Security =

        type Verifiable =
            | ObjectOfInterest of Types.V2.In.ObjectOfInterest
            | Pingback of Types.V2.In.Pingback
            | Action of Types.V2.In.Action
            | Location of Types.V2.In.PointLocation

        let verify (o : Verifiable) (ctx : CTX.t) (enc : Encoding) =
            let (datas, signature, timestamp) =
                match o with
                    | ObjectOfInterest v ->
                        ((sprintf "%s%i%s%s" v.URI v.timestamp v.userLevel v.username), v.signature, v.timestamp)
                    | Pingback v ->
                        ((sprintf "%s%i%i%s%s" v.URL v.datetime v.timestamp v.userLevel v.username), v.signature, v.timestamp)
                    | Action v ->
                        ((sprintf "%s%i%i%s%s" v.URI v.datetime v.timestamp v.userLevel v.username), v.signature, v.timestamp)
                    | Location v ->
                        ((sprintf "%s%i%i%i%i%s%s" v.source v.latitude v.longitude v.error v.timestamp v.userLevel v.username), v.signature, v.timestamp)

            try
                let data = enc.GetBytes(datas)
                if ctx.verifySignature data (signature :> obj) |> not then
                    raise (Err.Forbidden(Err.ForbiddenMinorCode.InvalidSignature))
            with 
                | :? HttpResponseException as e ->
                    raise e
                | _ ->
                    raise (Err.Forbidden(Err.ForbiddenMinorCode.InvalidSignature))
              
            if System.DateTime.UtcNow > ((In.datetime timestamp) + System.TimeSpan.FromMinutes(float 5)) then
                raise (Err.Forbidden(Err.ForbiddenMinorCode.Expired))

module Sec = Security

[<WU.ValidContextFilter()>]
type ObjectsOfInterestV2Controller() =
    inherit ApiController()

    [<HttpOptions>]
    [<ActionName("Default")>]
    [<WU.CacheHeaderFilter(365, 0, 0, 0)>] 
    member this.Options() = ()

    [<HttpGet>]
    [<ActionName("Default")>]
    member this.Get
            ([<ModelBinder(typeof<WU.ContextBinderProvider>)>] context : CTX.t) : obj =
        raise (Err.NotFound(Err.NotFoundMinorCode.Unspecified))

    [<HttpGet>]
    [<ActionName("Default")>]
    member this.Get
            ([<ModelBinder(typeof<WU.ContextBinderProvider>)>] context : CTX.t,
                [<ModelBinder(typeof<WU.DepthBinderProvider>)>] depth : int32,
                ID : int64) =
        let o = context.dataContext.ObjectOfInterests.SingleOrDefault((fun (o : ObjectOfInterest) -> o.ID = ID))
        match o with
            | null -> raise (Err.NotFound(Err.NotFoundMinorCode.Unspecified))
            | _ -> Out.objectOfInterest depth o

    [<HttpPost>]
    [<ActionName("Default")>]
    member this.Post
            ([<ModelBinder(typeof<WU.ContextBinderProvider>)>]context : CTX.t,
                interest : Types.V2.In.ObjectOfInterest,
                [<ModelBinder(typeof<WU.EncodingBinderProvider>)>]enc : Encoding) =
        
        Sec.verify (Sec.ObjectOfInterest(interest)) context enc

        let o = new ObjectOfInterest()
        o.ID <- context.getNextID(o :> obj)
        o.URI <- interest.URI
        context.dataContext.ObjectOfInterests.InsertOnSubmit(o)
        context.dataContext.SubmitChanges()
        Out.objectOfInterest 1 o

    [<HttpGet>]
    [<ActionName("Pingback")>]
    member this.GetPingback
            ([<ModelBinder(typeof<WU.ContextBinderProvider>)>] context : CTX.t,
                [<ModelBinder(typeof<WU.DepthBinderProvider>)>] depth : int32,
                oID : int64) =
        let o = context.dataContext.ObjectOfInterests.SingleOrDefault((fun (o : ObjectOfInterest) -> o.ID = oID))
        match o with
            | null -> raise (Err.NotFound(Err.NotFoundMinorCode.Unspecified))
            | _ -> o.PingbackObjects |> Seq.toList |> List.map (fun po -> Out.pingback depth po.Pingback)

    [<HttpGet>]
    [<ActionName("Pingback")>]
    member this.GetPingback
            ([<ModelBinder(typeof<WU.ContextBinderProvider>)>] context : CTX.t,
                [<ModelBinder(typeof<WU.DepthBinderProvider>)>] depth : int32,
                oID : int64,
                ID : int64) =
        let o = context.dataContext.ObjectOfInterests.SingleOrDefault((fun (o : ObjectOfInterest) -> o.ID = oID))
        if o = null then
            raise (Err.NotFound(Err.NotFoundMinorCode.Unspecified))
        let p = o.PingbackObjects.SingleOrDefault(fun po -> po.PingbackID = ID)
        if p = null then
            raise (Err.NotFound(Err.NotFoundMinorCode.Unspecified))            
        Out.pingback depth p.Pingback

    [<HttpPost>]
    [<ActionName("Pingback")>]
    member this.PostPingback
            ([<ModelBinder(typeof<WU.ContextBinderProvider>)>]context : CTX.t,
                [<ModelBinder(typeof<WU.EncodingBinderProvider>)>]enc : Encoding,
                oID : int64,
                pingback : Types.V2.In.Pingback) =
        
        let o = context.dataContext.ObjectOfInterests.SingleOrDefault((fun (o : ObjectOfInterest) -> o.ID = oID))
        if o = null then
            raise (Err.NotFound(Err.NotFoundMinorCode.Unspecified))

        Sec.verify (Sec.Pingback(pingback)) context enc

        let p = new Pingback()
        p.ID <- context.getNextID(p :> obj)
        p.URL <- pingback.URL
        p.DateTime <- In.datetime pingback.datetime
        context.dataContext.Pingbacks.InsertOnSubmit(p)
        
        let po = new PingbackObject()
        po.ID <- context.getNextID(po :> obj)
        po.ObjectOfInterest <- o
        po.Pingback <- p
        context.dataContext.PingbackObjects.InsertOnSubmit(po)

        context.dataContext.SubmitChanges()
        Out.pingback 1 p

    [<HttpPut>]
    [<ActionName("Pingback")>]
    member this.PutPingback
            ([<ModelBinder(typeof<WU.ContextBinderProvider>)>]context : CTX.t,
                [<ModelBinder(typeof<WU.EncodingBinderProvider>)>]enc : Encoding,
                oID : int64,
                ID : int64,
                pingback : Types.V2.In.Pingback) =
        
        let o = context.dataContext.ObjectOfInterests.SingleOrDefault((fun (o : ObjectOfInterest) -> o.ID = oID))
        if o = null then
            raise (Err.NotFound(Err.NotFoundMinorCode.Unspecified))

        let p = context.dataContext.Pingbacks.SingleOrDefault(fun (p : Pingback) -> p.ID = ID)
        if p = null then
            raise (Err.NotFound(Err.NotFoundMinorCode.Unspecified))

        Sec.verify (Sec.Pingback(pingback)) context enc

        let po = context.dataContext.PingbackObjects.SingleOrDefault(fun (po : PingbackObject) -> po.ObjectID = o.ID && po.PingbackID = p.ID)
        if po = null then
            let po = new PingbackObject()
            po.ID <- context.getNextID(po :> obj)
            po.ObjectOfInterest <- o
            po.Pingback <- p
            context.dataContext.PingbackObjects.InsertOnSubmit(po)

        context.dataContext.SubmitChanges()
        Out.pingback 1 p

    [<HttpGet>]
    [<ActionName("Action")>]
    member this.GetAction
            ([<ModelBinder(typeof<WU.ContextBinderProvider>)>] context : CTX.t,
                [<ModelBinder(typeof<WU.DepthBinderProvider>)>] depth : int32,
                oID : int64) =
        let o = context.dataContext.ObjectOfInterests.SingleOrDefault((fun (o : ObjectOfInterest) -> o.ID = oID))
        match o with
            | null -> raise (Err.NotFound(Err.NotFoundMinorCode.Unspecified))
            | _ -> o.Actions |> Seq.toList |> List.map (Out.action depth)

    [<HttpGet>]
    [<ActionName("Action")>]
    member this.GetAction
            ([<ModelBinder(typeof<WU.ContextBinderProvider>)>] context : CTX.t,
                [<ModelBinder(typeof<WU.DepthBinderProvider>)>] depth : int32,
                oID : int64,
                ID : int64) =
        let o = context.dataContext.ObjectOfInterests.SingleOrDefault((fun (o : ObjectOfInterest) -> o.ID = oID))
        if o = null then
            raise (Err.NotFound(Err.NotFoundMinorCode.Unspecified))
        let a = o.Actions.SingleOrDefault(fun a -> a.ID = ID)
        if a = null then
            raise (Err.NotFound(Err.NotFoundMinorCode.Unspecified))            
        Out.action depth a

    [<HttpPost>]
    [<ActionName("Action")>]
    member this.PostAction
            ([<ModelBinder(typeof<WU.ContextBinderProvider>)>]context : CTX.t,
                [<ModelBinder(typeof<WU.EncodingBinderProvider>)>]enc : Encoding,
                oID : int64,
                action : Types.V2.In.Action) =
        
        let o = context.dataContext.ObjectOfInterests.SingleOrDefault((fun (o : ObjectOfInterest) -> o.ID = oID))
        if o = null then
            raise (Err.NotFound(Err.NotFoundMinorCode.Unspecified))

        Sec.verify (Sec.Action(action)) context enc

        let userURI = sprintf "%s://%s" context.name action.username
        let u = match context.dataContext.Users.SingleOrDefault(fun (u : User) -> u.URI = userURI) with
                    | null -> 
                        let u = new User()
                        u.ID <- context.getNextID(u :> obj)
                        u.URI <- userURI
                        context.dataContext.Users.InsertOnSubmit(u)
                        context.dataContext.SubmitChanges()
                        u
                    | _ as u -> u

        let a = new Action()
        a.ID <- context.getNextID(a :> obj)
        a.URI <- action.URI
        a.User <- u
        a.ObjectOfInterest <- o

        let r = new Regex("^.*://(.*)$")
        let jo = JObject.Parse(r.Match(action.URI).Groups.[0].Value)
        match jo.["LocationID"] with
            | null -> ()
            | l ->
                let lid = System.Convert.ToInt64(l)
                let l = context.dataContext.Locations.SingleOrDefault(fun (l : Location) -> l.ID = lid)
                let al = new ActionLocation()
                al.ID <- context.getNextID(a :> obj)
                al.Action <- a
                al.Location <- l
                context.dataContext.ActionLocations.InsertOnSubmit(al)  

        context.dataContext.SubmitChanges()
        Out.action 1 a

    [<HttpGet>]
    [<ActionName("Location")>]
    member this.GetLocation
            ([<ModelBinder(typeof<WU.ContextBinderProvider>)>] context : CTX.t,
                [<ModelBinder(typeof<WU.DepthBinderProvider>)>] depth : int32,
                oID : int64) =
        let o = context.dataContext.ObjectOfInterests.SingleOrDefault((fun (o : ObjectOfInterest) -> o.ID = oID))
        match o with
            | null -> raise (Err.NotFound(Err.NotFoundMinorCode.Unspecified))
            | _ -> o.Locations |> Seq.toList |> List.map (fun l -> Out.location depth l)

    [<HttpGet>]
    [<ActionName("Location")>]
    member this.GetLocation
            ([<ModelBinder(typeof<WU.ContextBinderProvider>)>] context : CTX.t,
                [<ModelBinder(typeof<WU.DepthBinderProvider>)>] depth : int32,
                oID : int64,
                ID : int64) =
        let o = context.dataContext.ObjectOfInterests.SingleOrDefault((fun (o : ObjectOfInterest) -> o.ID = oID))
        if o = null then
            raise (Err.NotFound(Err.NotFoundMinorCode.Unspecified))
        let l = o.Locations.SingleOrDefault(fun l -> l.ID = ID)
        if l = null then
            raise (Err.NotFound(Err.NotFoundMinorCode.Unspecified))            
        Out.location depth l

    [<HttpPost>]
    [<ActionName("Location")>]
    member this.PostLocation
            ([<ModelBinder(typeof<WU.ContextBinderProvider>)>]context : CTX.t,
                [<ModelBinder(typeof<WU.EncodingBinderProvider>)>]enc : Encoding,
                oID : int64,
                location : Types.V2.In.PointLocation) =
        
        let o = context.dataContext.ObjectOfInterests.SingleOrDefault((fun (o : ObjectOfInterest) -> o.ID = oID))
        if o = null then
            raise (Err.NotFound(Err.NotFoundMinorCode.Unspecified))

        Sec.verify (Sec.Location(location)) context enc

        let userURI = sprintf "%s://%s" context.name location.username
        let u = match context.dataContext.Users.SingleOrDefault(fun (u : User) -> u.URI = userURI) with
                    | null -> 
                        let u = new User()
                        u.ID <- context.getNextID(u :> obj)
                        u.URI <- userURI
                        context.dataContext.Users.InsertOnSubmit(u)
                        context.dataContext.SubmitChanges()
                        u
                    | _ as u -> u

        let l = new Location()
        l.ID <- context.getNextID(l :> obj)
        l.LocationSource <- LocationSource.User
        l.ObjectOfInterest <- o
        context.dataContext.Locations.InsertOnSubmit(l)

        let lp = new LocationPoint()
        lp.ID <- context.getNextID(lp :> obj)
        lp.Error <- location.error
        lp.Center <- SqlGeography.Point(
                        Types.CoordAsFloat location.latitude,
                        Types.CoordAsFloat location.longitude,
                        int Types.SRIDS.WGS84)
        context.dataContext.LocationPoints.InsertOnSubmit(lp)

        l.LocationPoints.Add(lp)

        context.dataContext.SubmitChanges()
        Out.location 1 l

    (*[<HttpGet>]
    [<ActionName("LocationSearch")>] 
    member this.LocationSearch
            ([<ModelBinder(typeof<WU.ContextBinderProvider>)>]context : CTX.t,
                [<FromUri>]qp : Types.V1.OoIQueryParameters) =      
        let n = fun i -> new System.Nullable<float>(Types.CoordAsFloat i)
        context.dataContext.SelectObjectsWithinBounds(
                n qp.boundingBox.northEast.latitude,
                n qp.boundingBox.southWest.latitude,
                n qp.boundingBox.northEast.longitude,
                n qp.boundingBox.southWest.longitude,
                new System.Nullable<int64>(context.ID)) 
            |> Seq.map Conv.InBoundsResultToObjectRecord*)
