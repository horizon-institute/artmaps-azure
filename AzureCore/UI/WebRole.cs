using System;
using System.Collections.Generic;
using System.Linq;
using Microsoft.WindowsAzure;
using Microsoft.WindowsAzure.Diagnostics;
using Microsoft.WindowsAzure.ServiceRuntime;

namespace UI
{
    public class WebRole : RoleEntryPoint
    {
        public override bool OnStart()
        {
            ArtMaps.Azure.Utilities.Configuration.initDiagnostics();
            return base.OnStart();
        }
    }
}
