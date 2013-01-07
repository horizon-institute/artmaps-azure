using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.Http;
using System.Web.Mvc;
using System.Web.Routing;

namespace ArtMaps.UI
{
    public class RouteConfig
    {

        public static void RegisterRoutes(RouteCollection routes)
        {
            routes.IgnoreRoute("{resource}.axd/{*pathInfo}");

            var rts = new List<Route>();

            // Cross version
            rts.Add(routes.MapHttpRoute(
                name: "Admin",
                routeTemplate: "admin/rest/{version}/{action}",
                defaults: new { controller = "Admin", version = RouteParameter.Optional }
            ));

            rts.Add(routes.MapHttpRoute(
                name: "UsersSearch",
                routeTemplate: "service/{context}/rest/{version}/users/search",
                defaults: new { controller = "Users", action = "Search", version = RouteParameter.Optional }
            ));
            rts.Add(routes.MapHttpRoute(
                name: "UsersDefault",
                routeTemplate: "service/{context}/rest/{version}/users/{ID}",
                defaults: new { controller = "Users", action = "Default", ID = RouteParameter.Optional, version = RouteParameter.Optional }
            ));

            // Version 1
            rts.Add(routes.MapHttpRoute(
                name: "ExternalSearchV1",
                routeTemplate: "service/{context}/rest/v1/external/search",
                defaults: new { controller = "ExternalSearchV1", action = "Search" }
            ));
            rts.Add(routes.MapHttpRoute(
                name: "ActionsDefaultV1",
                routeTemplate: "service/{context}/rest/v1/objectsofinterest/{oID}/actions/{ID}",
                defaults: new { controller = "ActionsV1", action = "Default", ID = RouteParameter.Optional }
            ));
            rts.Add(routes.MapHttpRoute(
                name: "LocationsDefaultV1",
                routeTemplate: "service/{context}/rest/v1/objectsofinterest/{oID}/locations/{ID}",
                defaults: new { controller = "LocationsV1", action = "Default", ID = RouteParameter.Optional }
            ));
            rts.Add(routes.MapHttpRoute(
                name: "MetadataDefaultV1",
                routeTemplate: "service/{context}/rest/v1/objectsofinterest/{oID}/metadata",
                defaults: new { controller = "MetadataV1", action = "Default" }
            ));
            rts.Add(routes.MapHttpRoute(
                name: "ObjectsOfInterestSearchV1",
                routeTemplate: "service/{context}/rest/v1/objectsofinterest/search",
                defaults: new { controller = "ObjectsOfInterestV1", action = "Search" }
            ));
            rts.Add(routes.MapHttpRoute(
                name: "ObjectsOfInterestDefaultV1",
                routeTemplate: "service/{context}/rest/v1/objectsofinterest/{ID}",
                defaults: new { controller = "ObjectsOfInterestV1", action = "Default", ID = RouteParameter.Optional }
            ));
            rts.Add(routes.MapRoute(
                name: "ImportV1",
                url: "service/{context}/import/v1/csv/import",
                defaults: new { controller = "CsvImport", action = "Import" }
            ));

            foreach (var rt in rts)
            {
                if (rt.DataTokens == null)
                    rt.DataTokens = new RouteValueDictionary();
                rt.DataTokens["Namespaces"] =
                    rt.DataTokens["namespaces"] =
                        new string[] { "ArtMaps.Controllers" };
            }
        }
    }
}