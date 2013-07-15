namespace ArtMaps.Persistence.Context
{
    using System.Data.Linq;
    using System.Linq;

    public partial class ModelDataContext : System.Data.Linq.DataContext
    {
        public long GetNextID(object o)
        {
            return this.GetNextID(o.GetType().Name);
        }

        public long GetNextID(string tableName)
        {
            Entities.NextIDResult res = (this.NextID(tableName)).Single();
            return res.CurrentID;
        }
    }
}

namespace ArtMaps.Persistence.Entities
{
    using Microsoft.SqlServer.Types;
    using System.Data.Linq;
    using System.Linq;

    public enum MetadataValueType
    {
        TextPlain = -1,
        TextHTML,
        LinkDefault,
        LinkImage
    }

    partial class ObjectMetadata
    {
        public MetadataValueType ValueType
        {
            get { return (MetadataValueType)this.Type; }
            set { this.Type = (short)value; }
        }
    }

    public enum LocationSource
    {
        Unknown = -1,
        SystemImport,
        User
    }

    public enum LocationType
    {
        NotAssigned = -1,
        Point,
        Polygon,
        Ellipse,
        Named
    }

    partial class Location
    {
        public LocationSource LocationSource
        {
            get { return (LocationSource)this.Source; }
            set { this.Source = (short)value; }
        }

        public LocationType LocationType
        {
            get
            {
                if (this.LocationPoint != null)
                    return LocationType.Point;
                if (this.LocationPolygon != null)
                    return LocationType.Polygon;
                if (this.LocationEllipse != null)
                    return LocationType.Ellipse;
                if (this.LocationNamed != null)
                    return LocationType.Named;
                return LocationType.NotAssigned;
            }
        }

        public LocationPoint LocationPoint
        {
            get { return this.LocationPoints.FirstOrDefault(l => true); }
        }

        public LocationPolygon LocationPolygon
        {
            get { return this.LocationPolygons.FirstOrDefault(l => true); }
        }

        public LocationEllipse LocationEllipse
        {
            get { return this.LocationEllipses.FirstOrDefault(l => true); }
        }

        public LocationNamed LocationNamed
        {
            get { return this.LocationNameds.FirstOrDefault(l => true); }
        }
    }

    partial class LocationPoint
    {
        public SqlGeography Center
        {
            get { return SqlGeography.Parse(this.CenterText); }
            set { this.CenterText = value.STAsText().ToSqlString().Value; }
        }
    }

    partial class LocationPolygon
    {
        public SqlGeography Points
        {
            get { return SqlGeography.Parse(this.PointsText); }
            set { this.PointsText = value.STAsText().ToSqlString().Value; }
        }
    }

    partial class LocationEllipse
    {
        public SqlGeography Origin
        {
            get { return SqlGeography.Parse(this.OriginText); }
            set { this.OriginText = value.STAsText().ToSqlString().Value; }
        }
    }
}
