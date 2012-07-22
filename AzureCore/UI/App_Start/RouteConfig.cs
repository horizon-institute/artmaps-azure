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

            rts.Add(routes.MapHttpRoute(
                name: "Admin",
                routeTemplate: "admin/rest/v1/{action}",
                defaults: new { controller = "Admin" }
            ));
            
            
            rts.Add(routes.MapHttpRoute(
                name: "ActionsDefault",
                routeTemplate: "service/{context}/rest/v1/objectsofinterest/{oID}/actions/{ID}",
                defaults: new { controller = "Actions", action = "Default", ID = RouteParameter.Optional }
            ));
            rts.Add(routes.MapHttpRoute(
                name: "LocationsDefault",
                routeTemplate: "service/{context}/rest/v1/objectsofinterest/{oID}/locations/{ID}",
                defaults: new { controller = "Locations", action = "Default", ID = RouteParameter.Optional }
            ));
            rts.Add(routes.MapHttpRoute(
                name: "MetadataDefault",
                routeTemplate: "service/{context}/rest/v1/objectsofinterest/{oID}/metadata",
                defaults: new { controller = "Metadata", action = "Default" }
            ));
            rts.Add(routes.MapHttpRoute(
                name: "ObjectsOfInterestSearch",
                routeTemplate: "service/{context}/rest/v1/objectsofinterest/search",
                defaults: new { controller = "ObjectsOfInterest", action = "Search" }
            ));
            rts.Add(routes.MapHttpRoute(
                name: "ObjectsOfInterestDefault",
                routeTemplate: "service/{context}/rest/v1/objectsofinterest/{ID}",
                defaults: new { controller = "ObjectsOfInterest", action = "Default", ID = RouteParameter.Optional }
            ));

            foreach(var rt in rts) 
                rt.DataTokens["Namespaces"] =
                    rt.DataTokens["namespaces"] = 
                        new string[] { "ArtMaps.Controllers" };
        }
    }
}