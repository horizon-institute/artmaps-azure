#light

module ArtMaps.Controllers.Metadata

module Filters =

    open HtmlAgilityPack
    open System
    open System.IO
    open System.Net
    open System.Text.RegularExpressions

    [<AttributeUsage(AttributeTargets.Method, Inherited = true, AllowMultiple = true)>]
    type FilterUriMatch(pattern : string) =
        inherit Attribute()
        let regex = new Regex(pattern)
        member this.IsFilterFor(s : string) = regex.IsMatch(s)
        override this.Equals(other) = this.GetHashCode() = other.GetHashCode()
        override this.GetHashCode() = base.GetHashCode()
        interface IComparable<FilterUriMatch> with
            member this.CompareTo(other) = this.GetHashCode().CompareTo(other.GetHashCode)
        interface IComparable with
            member this.CompareTo(other) = this.GetHashCode().CompareTo(other.GetHashCode)

    [<FilterUriMatch("^tatecollection://.*$")>]
    let TateCollectionFilter (uri : string) =
        let USER_AGENT = @"ArtMapsCore/TateCollectionFilter/V1"
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
                let imageurl = (IMAGE_URL (root.SelectNodes("//div[@class='image_box']/a/img").[0].Attributes.["src"].Value)) :> obj
                [("imageurl", imageurl)] |> Map.ofList
            with _ -> Map.empty<string, obj>

        let paths = [
            ("artist", "//div[@id='region-sidebar-artwork']//span[@class='infoWorkArtName']")
            ("artistdate", "//div[@id='region-sidebar-artwork']//span[@class='infoWorkArtDates']")   
            ("title", "//div[@id='region-sidebar-artwork']//div[@class='infoTitle infoValue']")   
            ("artworkdate", "//div[@id='region-sidebar-artwork']//span[@class='infoValue infoDate']/span[1]")   
            ("reference", "//div[@id='region-sidebar-artwork']//div[@class='infoAcNo infoCollData']/span[1]/span[1]")
        ] 
        paths
            |> List.fold (
                fun (acc : Map<string, obj>) (name, path) -> 
                    try
                        acc.Add(name, (doc.DocumentNode.SelectNodes(path) |> Seq.head).InnerText :> obj)
                    with _ -> acc
                ) metadata

open System.Reflection

module F = Filters
module Log = ArtMaps.Utilities.Log

let FilterMap =
    try
        let getfilters (m : MethodInfo) (acc : Map<Filters.FilterUriMatch, (string -> Map<string, obj>)>) =
            let filterfunc (s : string) =  m.Invoke(null, [| s |]) :?> Map<string, obj>
            m.GetCustomAttributes(typeof<Filters.FilterUriMatch>, false)
                |> Array.fold 
                (fun (accc : Map<Filters.FilterUriMatch, (string -> Map<string, obj>)>) a -> 
                    let matcher = a :?> Filters.FilterUriMatch
                    accc.Add(matcher, filterfunc)) acc

        let mmod = Assembly.GetExecutingAssembly().GetTypes() 
                    |> Array.find (fun t -> (sprintf "%s.%s" t.Namespace t.Name) = "ArtMaps.Controllers.Metadata")
        let fmod = mmod.GetNestedType("Filters")
        fmod.GetMethods() 
            |> Array.fold (fun acc m -> getfilters m acc) Map.empty<Filters.FilterUriMatch, (string -> Map<string, obj>)>
    with _ as e ->
        Map.empty<Filters.FilterUriMatch, (string -> Map<string, obj>)>

let GetFilters (uri : string) =
    FilterMap |> Map.fold (fun acc k v -> if k.IsFilterFor uri then List.Cons(v, acc) else acc) List.empty<(string -> Map<string, obj>)>
