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
            var ss = new Newtonsoft.Json.JsonSerializerSettings();
            ss.Converters.Add(new ArtMaps.Controllers.Types.V1.JsonConverters.ActionConverter());
            ss.Converters.Add(new ArtMaps.Controllers.Types.V1.JsonConverters.PointLocationConverter());
            ss.Converters.Add(new ArtMaps.Controllers.Types.V1.JsonConverters.ObjectOfInterestConverter());
            ss.Converters.Add(new ArtMaps.Controllers.Types.V1.JsonConverters.UserConverter());
            ss.Converters.Add(new ArtMaps.Controllers.Types.V1.JsonConverters.ObjectOfInterestSearchConverter());
            ss.Converters.Add(new ArtMaps.Utilities.Web.JsonRecordConverter());
            GlobalConfiguration.Configuration.Formatters.JsonFormatter.SerializerSettings = ss;
            GlobalConfiguration.Configuration.IncludeErrorDetailPolicy = IncludeErrorDetailPolicy.Always;
            GlobalConfiguration.Configuration.Filters.Add(new ArtMaps.Utilities.Web.ExceptionLoggingFilter());
            System.Threading.ThreadPool.SetMinThreads(20, 20);
        }
    }
}