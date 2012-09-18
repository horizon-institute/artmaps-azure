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
            ss.Converters.Add(new ArtMaps.Controllers.Types.JsonConverters.ActionConverter());
            ss.Converters.Add(new ArtMaps.Controllers.Types.JsonConverters.PointLocationConverter());
            ss.Converters.Add(new ArtMaps.Controllers.Types.JsonConverters.ObjectOfInterestConverter());
            ss.Converters.Add(new ArtMaps.Controllers.Types.JsonConverters.UserConverter());
            ss.Converters.Add(new ArtMaps.Utilities.Web.RecordConverter());
            GlobalConfiguration.Configuration.Formatters.JsonFormatter.SerializerSettings = ss;

            if (RoleEnvironment.IsEmulated)
            {
                var logname = "UI.Global." + RoleEnvironment.CurrentRoleInstance.Id + ".log";
                var path = System.IO.Path.Combine(
                    ArtMaps.Azure.Utilities.Configuration.Value<string>("ArtMaps.DevFabric.Tracing.Path"),
                    logname);
                System.Diagnostics.Trace.Listeners.Add(
                    new ArtMaps.Azure.Utilities.Configuration.DevFabricTraceListener(
                        path,
                        logname));
            }

            GlobalConfiguration.Configuration.IncludeErrorDetailPolicy = IncludeErrorDetailPolicy.Always;
            GlobalConfiguration.Configuration.Filters.Add(new ArtMaps.Utilities.Web.ExceptionLoggingFilter());
            System.Threading.ThreadPool.SetMinThreads(20, 20);
        }
    }
}