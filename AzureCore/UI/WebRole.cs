using System;
using System.Collections.Generic;
using System.Linq;
using Microsoft.WindowsAzure;
using Microsoft.WindowsAzure.Diagnostics;
using Microsoft.WindowsAzure.ServiceRuntime;

namespace ArtMaps.UI
{
    public class WebRole : RoleEntryPoint
    {
        public override bool OnStart()
        {
            ArtMaps.Azure.Utilities.Configuration.Diagnostics();
            return base.OnStart();
        }
    }
}