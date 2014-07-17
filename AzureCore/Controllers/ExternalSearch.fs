#light

module ArtMaps.Controllers.ExternalSearch

module Searches =

    module Conv = ArtMaps.Controllers.Types.V1.Conversions
    module CTX = ArtMaps.Context
    module Log = ArtMaps.Utilities.Log

    open ArtMaps.Persistence.Entities
    open HtmlAgilityPack
    open Newtonsoft.Json
    open Newtonsoft.Json.Linq
    open System
    open System.Linq
    open System.IO
    open System.Net
    open System.Text.RegularExpressions

    [<AttributeUsage(AttributeTargets.Method, Inherited = true, AllowMultiple = true)>]
    type SearchUriMatch(pattern : string, priority : int) =
        inherit Attribute()
        let regex = new Regex(pattern)
        new (pattern : string) = new SearchUriMatch(pattern, 1)
        member this.Pattern = pattern
        member this.IsSearchFor(s : string) = 
            regex.IsMatch(s)
        member this.Priority = priority
        override this.Equals(other) = this.GetHashCode() = other.GetHashCode()
        override this.GetHashCode() = base.GetHashCode()
        interface IComparable<SearchUriMatch> with
            member this.CompareTo(other) = 
                this.Pattern.CompareTo(other.Pattern)
        interface IComparable with
            member this.CompareTo(other) = 
                match other with 
                    | :? SearchUriMatch as o -> this.Pattern.CompareTo(o.Pattern)
                    | _ -> -1

    type artist = {
            name : string
            ID : int64
        }

    [<SearchUriMatch("^tateartist://.*$")>]
    let TateArtistSearch (uri : string, pageno : int32, ctx : CTX.t) =
        let ref = uri.Substring(uri.LastIndexOf("/") + 1).Trim()
        let USER_AGENT = @"ArtMapsCore/1.0"
        let BASE_URL = "http://www.tate.org.uk"
        let SEARCH_URL = sprintf "%s/art/artists?wv=list&q=%s&ap=%i" BASE_URL ref
        
        let getpage page = 
            sprintf "Fetching page %i for search '%s'" page uri |> Log.information
            let req = WebRequest.Create(SEARCH_URL page) :?> HttpWebRequest
            req.UserAgent <- USER_AGENT
            req.KeepAlive <- true
            req.Headers.Set("Pragma", "no-cache")
            req.Timeout <- 300000
            req.Method <- "GET"
            let res = req.GetResponse()
            let doc = new HtmlDocument()
            doc.Load(new StreamReader(res.GetResponseStream()))
            res.Close()
            doc

        let getartists (page : HtmlDocument) =
            let pattern = sprintf "tatecollection://%s"
            page.DocumentNode.SelectNodes(
                    "//*[@id=\"zone-content\"]"
                    + "//div[contains(@class, \"explorerResults\")]"
                    + "//div[contains(@class, \"type1-artist\")]")
                |> Seq.map (
                        fun n -> 
                            {
                                artist.name = n.SelectSingleNode(".//a[contains(@class, \"artist-name\")]//span").InnerText
                                ID = Convert.ToInt64(n.Id.Replace("_", ""))
                            }
                        )
                |> List.ofSeq |> Seq.ofList

        try
            let min = Math.Max(1, (((pageno - 1) * 3) + 1))
            let max = min + 2
            sprintf "Page count for search '%s': %i -> %i" uri min max |> Log.information
            seq { min..max } 
                |> Seq.map (fun i -> async { 
                                        return 
                                            try 
                                                i |> getpage |> getartists 
                                            with _ as e -> 
                                                //sprintf "Unable to get page %i for search '%s': %s\n%s" 
                                                    //i uri e.Message e.StackTrace |> Log.Warning
                                                Seq.empty })
                |> Async.Parallel
                |> Async.RunSynchronously
                |> Seq.ofArray
                |> Seq.concat
        with _ as e -> 
            sprintf "Fail: %s\n%s" e.Message e.StackTrace |> Log.error
            Seq.empty
        
    [<SearchUriMatch("^tateartwork://.*$")>]
    let TateArtworkSearch (uri : string, pageno : int32, ctx : CTX.t) =
        let ref = uri.Substring(uri.LastIndexOf("/") + 1).Trim()
        let USER_AGENT = @"ArtMapsCore/1.0"
        let BASE_URL = "http://www.tate.org.uk"
        let SEARCH_URL = sprintf "%s/art/artworks?wv=list&q=%s&wp=%i" BASE_URL ref
        
        let getpage page = 
            sprintf "Fetching page %i for search '%s'" page uri |> Log.information
            let req = WebRequest.Create(SEARCH_URL page) :?> HttpWebRequest
            req.UserAgent <- USER_AGENT
            req.KeepAlive <- true
            req.Headers.Set("Pragma", "no-cache")
            req.Timeout <- 300000
            req.Method <- "GET"
            let res = req.GetResponse()
            let doc = new HtmlDocument()
            doc.Load(new StreamReader(res.GetResponseStream()))
            res.Close()
            doc

        let getartworks (page : HtmlDocument) =
            let pattern = sprintf "tatecollection://%s"
            page.DocumentNode.SelectNodes(
                    "//*[@id=\"zone-content\"]"
                    + "//ul[contains(@class, \"explorerList\")]"
                    + "//li[contains(@class, \"list-work\")]"
                    + "//div[@class=\"ref\"]"
                    + "//span[contains(@class, \"acno\")]")
                |> Seq.map (fun n -> n.InnerText)
                |> Seq.choose (
                fun r -> 
                    let uri = pattern r
                    try
                        let o = ctx.dataContext.ObjectOfInterests.Single(
                                        fun (o : ObjectOfInterest) -> o.URI = uri)
                        let oo = o |> Conv.ObjectToObjectSearchRecord
                        o.Actions |> Seq.iter (fun i -> ())
                        
                        Some(oo)
                    with _ as e ->
                        //sprintf "Unable to find ObjectOfInterest for URI '%s'" uri |> Log.Warning
                        None
                ) |> List.ofSeq |> Seq.ofList

        try
            let min = Math.Max(1, (((pageno - 1) * 3) + 1))
            let max = min + 2
            sprintf "Page count for search '%s': %i -> %i" uri min max |> Log.information
            seq { min..max } 
                |> Seq.map (fun i -> async { 
                                        return 
                                            try 
                                                i |> getpage |> getartworks 
                                            with _ as e -> 
                                                sprintf "Unable to get page %i for search '%s': %s\n%s" 
                                                    i uri e.Message e.StackTrace |> Log.warning
                                                Seq.empty })
                |> Async.Parallel
                |> Async.RunSynchronously
                |> Seq.ofArray
                |> Seq.concat
        with _ -> 
            Seq.empty

    [<SearchUriMatch("^tateartistartwork://.*$")>]
    let TateArtistArtworkSearch (uri : string, pageno : int32, ctx : CTX.t) =
        let ref = uri.Substring(uri.LastIndexOf("/") + 1).Trim()
        let USER_AGENT = @"ArtMapsCore/1.0"
        let BASE_URL = "http://www.tate.org.uk"
        let SEARCH_URL = sprintf "%s/art/artworks?wv=list&aid=%s&wp=%i" BASE_URL ref
        
        let getpage page = 
            sprintf "Fetching page %i for search '%s'" page uri |> Log.information
            let req = WebRequest.Create(SEARCH_URL page) :?> HttpWebRequest
            req.UserAgent <- USER_AGENT
            req.KeepAlive <- true
            req.Headers.Set("Pragma", "no-cache")
            req.Timeout <- 300000
            req.Method <- "GET"
            let res = req.GetResponse()
            let doc = new HtmlDocument()
            doc.Load(new StreamReader(res.GetResponseStream()))
            res.Close()
            doc

        let getartworks (page : HtmlDocument) =
            let pattern = sprintf "tatecollection://%s"
            page.DocumentNode.SelectNodes(
                    "//*[@id=\"zone-content\"]"
                    + "//ul[contains(@class, \"explorerList\")]"
                    + "//li[contains(@class, \"list-work\")]"
                    + "//div[@class=\"ref\"]"
                    + "//span[contains(@class, \"acno\")]")
                |> Seq.map (fun n -> n.InnerText)
                |> Seq.choose (
                fun r -> 
                    let uri = pattern r
                    try
                        let o = ctx.dataContext.ObjectOfInterests.Single(
                                        fun (o : ObjectOfInterest) -> o.URI = uri)
                        let oo = o |> Conv.ObjectToObjectSearchRecord
                        o.Actions |> Seq.iter (fun i -> ())
                        
                        Some(oo)
                    with _ as e ->
                        //sprintf "Unable to find ObjectOfInterest for URI '%s'" uri |> Log.Warning
                        None
                ) |> List.ofSeq |> Seq.ofList

        try
            let min = ((pageno - 1) * 3) + 1
            let max = min + 2
            sprintf "Page count for search '%s': %i -> %i" uri min max |> Log.information
            seq { min..max } 
                |> Seq.map (fun i -> async { 
                                        return 
                                            try 
                                                i |> getpage |> getartworks 
                                            with _ as e -> 
                                                sprintf "Unable to get page %i for search '%s': %s\n%s" 
                                                    i uri e.Message e.StackTrace |> Log.warning
                                                Seq.empty })
                |> Async.Parallel
                |> Async.RunSynchronously
                |> Seq.ofArray
                |> Seq.concat
        with _ -> 
            Seq.empty

    [<SearchUriMatch("^artmaps://.*$")>]
    let ArtmapsSearch (uri : string, pageno : int32, ctx : CTX.t) =
        let ref = uri.Substring(uri.LastIndexOf("/") + 1).Trim().ToLower()
        let start = pageno * 10
        let q = query { for md in ctx.dataContext.ObjectMetadatas do  
                            if md.Value.ToLower().Contains(ref) then select md.ObjectOfInterest }
        q |> Seq.skip start |> Seq.take 10 |> Seq.toList |> List.map (fun o -> 
                                let o' = Conv.ObjectToObjectSearchRecord o
                                o.Actions |> Seq.iter (fun i -> ())
                                o') |> Seq.ofList


open System
open System.Collections.Generic
open System.Reflection

module CTX = ArtMaps.Context
module Log = ArtMaps.Utilities.Log
module S = Searches

type Search = string * int32 * CTX.t -> obj

let DefaultSearch = new KeyValuePair<int, Search>(Int32.MaxValue, (fun args -> null))

let SearchMap =
    try
        let getSearches (acc : Map<S.SearchUriMatch, Search>) (m : MethodInfo) =
            let searchfunc (s : string, p : int32, ctx : CTX.t) =  m.Invoke(null, [| s; p; ctx |]) 
            m.GetCustomAttributes(typeof<S.SearchUriMatch>, false)
            |> Array.fold 
                (fun (accc : Map<S.SearchUriMatch, Search>) a -> 
                    let matcher = a :?> S.SearchUriMatch
                    Map.add matcher searchfunc accc)
                acc

        let mmod = Assembly.GetExecutingAssembly().GetTypes() 
                    |> Array.find (fun t -> (sprintf "%s.%s" t.Namespace t.Name) = "ArtMaps.Controllers.ExternalSearch")
        let fmod = mmod.GetNestedType("Searches")
        fmod.GetMethods() |> Array.fold getSearches Map.empty<S.SearchUriMatch, Search>
    with _ as e ->
        sprintf "%s\n%s" e.Message e.StackTrace |> Log.error
        Map.empty<S.SearchUriMatch, Search>

let GetSearch (uri : string) =
    (SearchMap 
        |> Map.fold (
            fun functions k v -> 
                if k.IsSearchFor uri 
                    then functions |> Map.add k.Priority v
                    else functions
        ) 
            Map.empty<int, Search>
        |> Seq.fold (
            fun (search : KeyValuePair<int, Search>) v -> 
                if v.Key < search.Key
                    then v
                    else search) 
            DefaultSearch
        ).Value
        