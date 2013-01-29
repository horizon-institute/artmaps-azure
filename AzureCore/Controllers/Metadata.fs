#light

module ArtMaps.Controllers.Metadata

module CTX = ArtMaps.Context

type t = {
    ID : int64
    name : string
    value : string
    valueType : string
}


module Filters =

    open ArtMaps.Persistence.Entities
    open HtmlAgilityPack
    open System
    open System.Data.Linq
    open System.IO
    open System.Linq
    open System.Net
    open System.Text.RegularExpressions

    module Util = ArtMaps.Utilities.General

    [<AttributeUsage(AttributeTargets.Method, Inherited = true, AllowMultiple = true)>]
    type FilterUriMatch(pattern : string) =
        inherit Attribute()
        let regex = new Regex(pattern)
        member this.IsFilterFor(s : string) = regex.IsMatch(s)
        override this.Equals(other) = this.GetHashCode() = other.GetHashCode()
        override this.GetHashCode() = base.GetHashCode()
        interface IComparable<FilterUriMatch> with
            member this.CompareTo(other) = this.GetHashCode().CompareTo(other.GetHashCode())
        interface IComparable with
            member this.CompareTo(other) = this.GetHashCode().CompareTo(other.GetHashCode())

    [<Literal>] 
    let ArtMapsFilterRegexString = @"^artmaps:\/\/(.*)$"
    let ArtMapsFilterRegex = new Regex(ArtMapsFilterRegexString)
    [<FilterUriMatch(ArtMapsFilterRegexString)>]
    let ArtMapsFilter (context : CTX.t) (uri : string) =
        let o = context.dataContext.ObjectOfInterests.Single(fun (o : ObjectOfInterest) -> o.URI = uri)
        let conv (om : ObjectMetadata) =
            {
                t.ID = om.ID
                name = om.Name
                value = om.Value
                valueType = Util.enumName om.ValueType
            }
        o.ObjectMetadatas |> Seq.map conv |> List.ofSeq

    [<FilterUriMatch("^tatecollection://.*$")>]
    let TateCollectionFilter (context : CTX.t) (uri : string) =
        let USER_AGENT = @"ArtMapsCore/1.0"
        let BASE_URL = "http://www.tate.org.uk"
        let ARTWORK_URL = sprintf "%s/art/artworks/%s" BASE_URL
        let IMAGE_URL = sprintf "%s%s" BASE_URL

        let ref = uri.Substring(uri.LastIndexOf("/") + 1).Trim()
        let req = WebRequest.Create(ARTWORK_URL ref) :?> HttpWebRequest
        req.UserAgent <- USER_AGENT
        req.KeepAlive <- true
        req.Headers.Set("Pragma", "no-cache")
        req.Timeout <- 300000
        req.Method <- "GET"
        let res = req.GetResponse()
        let doc = new HtmlDocument()
        doc.Load(new StreamReader(res.GetResponseStream()))
        res.Close()

        let root = doc.DocumentNode

        let metadata =
            try
                let imageurl = (IMAGE_URL (root.SelectNodes("//div[@class='image_box']//img").[0].Attributes.["src"].Value))
                [{ t.ID = -1L; name = "imageurl"; t.value = imageurl; t.valueType = Enum.GetName(typeof<MetadataValueType>, MetadataValueType.LinkImage)}]
            with _ -> List.empty<t>

        let paths = [
            ("artist", "//div[@id='region-sidebar-artwork']//span[@class='infoWorkArtName']")
            ("artistdate", "//div[@id='region-sidebar-artwork']//span[@class='infoWorkArtDates']")   
            ("title", "//div[@id='region-sidebar-artwork']//div[@class='infoTitle infoValue']")   
            ("artworkdate", "//div[@id='region-sidebar-artwork']//span[@class='infoValue infoDate']/span[1]")   
            ("reference", "//div[@id='region-sidebar-artwork']//div[@class='infoAcNo infoCollData']/span[1]/span[1]")
        ] 
        paths
            |> List.fold (
                fun (acc : t list) (name, path) -> 
                    try
                        acc @ [{ 
                                t.ID = -1L;
                                t.name = name;
                                t.value = (doc.DocumentNode.SelectNodes(path) |> Seq.head).InnerText;
                                t.valueType = Enum.GetName(typeof<MetadataValueType>, MetadataValueType.TextPlain)}]
                    with _ -> acc
                ) metadata


open System.Reflection

module F = Filters
module Log = ArtMaps.Utilities.Log

type FilterFun = CTX.t -> string -> t list

let FilterMap =
    try
        let getfilters (m : MethodInfo) (acc : Map<Filters.FilterUriMatch, FilterFun>) =
            let filterfunc (c : CTX.t) (s : string) =  m.Invoke(null, [| c; s |]) :?> t list
            m.GetCustomAttributes(typeof<Filters.FilterUriMatch>, true)
                |> Array.fold 
                (fun (accc : Map<Filters.FilterUriMatch, FilterFun>) a -> 
                    let matcher = a :?> Filters.FilterUriMatch
                    accc.Add(matcher, filterfunc)) acc

        let mmod = Assembly.GetExecutingAssembly().GetTypes() 
                    |> Array.find (fun t -> (sprintf "%A" t) = "ArtMaps.Controllers.Metadata")
        let fmod = mmod.GetNestedType("Filters")
        fmod.GetMethods()
            |> Array.fold (fun acc m -> getfilters m acc) Map.empty<Filters.FilterUriMatch, FilterFun>
    with _ as e ->
        sprintf "%s\n%s" e.Message e.StackTrace |> Log.error
        Map.empty<Filters.FilterUriMatch, FilterFun>

let filtersFor (uri : string) =
    FilterMap |> Map.fold (fun acc k v -> if k.IsFilterFor uri then List.Cons(v, acc) else acc) List.empty<FilterFun>

let fetch (context : CTX.t) (uri : string) = 
    let filters = filtersFor uri
    match filters.IsEmpty with
        | true -> List<t>.Empty
        | _ -> filters.[0] context uri

let fetchV1 (context : CTX.t) (uri : string) = 
    let md = fetch context uri
    md |> List.map (fun d -> (d.name, d.value :> obj)) |> Map.ofList
