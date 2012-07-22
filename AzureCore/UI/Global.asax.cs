using Microsoft.WindowsAzure.ServiceRuntime;
using Newtonsoft.Json;
using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.Http;
using System.Web.Mvc;
using System.Web.Routing;

namespace ArtMaps.UI
{
    public class MvcApplication : System.Web.HttpApplication
    {
        protected void Application_Start()
        {
            AreaRegistration.RegisterAllAreas();

            FilterConfig.RegisterGlobalFilters(GlobalFilters.Filters);
            RouteConfig.RegisterRoutes(RouteTable.Routes);

            ArtMaps.Azure.Utilities.Configuration.Settings();
            ArtMaps.Azure.Utilities.Configuration.Storage();

            var ss = GlobalConfiguration.Configuration.Formatters.JsonFormatter.SerializerSettings;
            if (ss == null)
                ss = new Newtonsoft.Json.JsonSerializerSettings();
            ss.Converters.Add(new ArtMaps.Utilities.Web.RecordConverter());
            GlobalConfiguration.Configuration.Formatters.JsonFormatter.SerializerSettings = ss;

            if (RoleEnvironment.IsEmulated)
            {
                var path = System.IO.Path.Combine(
                    ArtMaps.Azure.Utilities.Configuration.Value<string>("ArtMaps.DevFabric.Tracing.Path"),
                    "UI.Global.log");
                System.Diagnostics.Trace.Listeners.Add(
                    new ArtMaps.Azure.Utilities.Configuration.DevFabricTraceListener(
                        path,
                        "UI.Global.log"));
            }

            GlobalConfiguration.Configuration.IncludeErrorDetailPolicy = IncludeErrorDetailPolicy.LocalOnly;
        }
    }
}