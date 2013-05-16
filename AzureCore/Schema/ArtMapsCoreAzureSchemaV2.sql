/****** Create Federation Scheme ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE FEDERATION ContextFederation(ContextID BIGINT RANGE)
GO

/****** Route script to the Federation member ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
USE FEDERATION ContextFederation(ContextID=0) WITH RESET, FILTERING=OFF
GO

/****** Object:  StoredProcedure [dbo].[NextID]    Script Date: 12/11/2012 11:53:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE PROCEDURE [dbo].[NextID]
	-- Add the parameters for the stored procedure here
	@TableName NVARCHAR(50) = 'Unknown'
AS
BEGIN
	BEGIN TRANSACTION
	-- SET NOCOUNT ON added to prevent extra result sets from
	-- interfering with SELECT statements.
	SET NOCOUNT ON;
	-- Insert statements for procedure here
	IF (SELECT COUNT(1) FROM [Sequence] WHERE [Sequence].[TableName] = @TableName) = 0
	BEGIN
		INSERT INTO [Sequence] ([TableName], [CurrentID]) VALUES (@TableName, -1)
	END
	UPDATE [Sequence] SET [CurrentID] = [CurrentID] + 1 WHERE [TableName] = @TableName
	SELECT [CurrentID] FROM [Sequence] WHERE [Sequence].[TableName] = @TableName
	COMMIT TRANSACTION
END

GO
/****** Object:  StoredProcedure [dbo].[SelectObjectsWithinBounds]    Script Date: 12/11/2012 11:53:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE PROCEDURE [dbo].[SelectObjectsWithinBounds]
	@north float,
	@south float,
	@east float,
	@west float,
	@contextID bigint
AS
BEGIN
	SET NOCOUNT ON;
	DECLARE @area geography;
	DECLARE @areapoly nvarchar(MAX);
	DECLARE @fetchall bit;
	SET @areapoly = 'POLYGON((:west :south, :east :south, :east :north, :west :north, :west :south))'
	SET @areapoly = REPLACE(@areapoly, ':north', CONVERT(nvarchar(MAX), @north))
	SET @areapoly = REPLACE(@areapoly, ':south', CONVERT(nvarchar(MAX), @south))
	SET @areapoly = REPLACE(@areapoly, ':east', CONVERT(nvarchar(MAX), @east))
	SET @areapoly = REPLACE(@areapoly, ':west', CONVERT(nvarchar(MAX), @west))
	BEGIN TRY
		SET @area = geography::STGeomFromText(@areapoly, 4326);
		SET @fetchall = 0;
	END TRY
	BEGIN CATCH
		SET @fetchall = 1;
	END CATCH

	CREATE TABLE #objectsr (
		ID BIGINT NOT NULL,
		ContextID BIGINT NOT NULL,
		URI NVARCHAR(MAX) NOT NULL,
		Locations NVARCHAR(MAX),
		Actions NVARCHAR(MAX));
	DECLARE @objectid BIGINT;
	IF @fetchall = 0
	BEGIN
		DECLARE objectsc CURSOR FOR 
		SELECT
			DISTINCT l.ObjectID AS ObjectID
		FROM
			Location l, LocationPoint lp
		WHERE
			l.ID = lp.LocationID
			AND l.ContextID = @ContextID
			AND @area.STContains(lp.Center) = 1;
	END
	ELSE
	BEGIN
		DECLARE objectsc CURSOR FOR 
		SELECT
			DISTINCT l.ObjectID AS ObjectID
		FROM
			Location l, LocationPoint lp
		WHERE
			l.ID = lp.LocationID
			AND l.ContextID = @ContextID;
	END
	OPEN objectsc;
	FETCH NEXT FROM objectsc INTO @objectid;
	WHILE @@FETCH_STATUS = 0
		BEGIN
			INSERT INTO #objectsr(ID, ContextID, URI, Locations, Actions) 
				SELECT o.ID, o.ContextID, o.URI,
					(SELECT 
							l.ID AS ID,
							l.Source AS Source,
							lp.CenterText AS CenterText,
							lp.Error AS Error
						FROM 
							Location l,
							LocationPoint lp
						WHERE 
							l.ObjectID = o.ID 
							AND l.ID = lp.LocationID
						FOR XML PATH ('Location'), ROOT ('Locations')) AS Locations,
					(SELECT 
							a.ID AS ID,
							a.URI AS URI,
							a.UserID AS UserID,
							a.[DateTime] AS [DateTime] 
						FROM [Action] a 
						WHERE a.ObjectID = o.ID 
						FOR XML PATH ('Action'), ROOT ('Actions')) AS Actions
				FROM ObjectOfInterest o 
				WHERE o.ID = @objectid;
			FETCH NEXT FROM objectsc INTO @objectid;
		END;
	CLOSE objectsc;
	DEALLOCATE objectsc;
	SELECT * FROM #objectsr;
	DROP TABLE #objectsr;
END

GO
/****** Object:  StoredProcedure [dbo].[SelectObjectsWithinBounds2]    Script Date: 19/11/2012 11:19:33 ******/
SET ANSI_NULLS ON
GO

SET QUOTED_IDENTIFIER ON
GO


CREATE PROCEDURE [dbo].[SelectObjectsWithinBoundsV2]
	@north float,
	@south float,
	@east float,
	@west float,
	@contextID bigint
AS
BEGIN
	SET NOCOUNT ON;
	DECLARE @area geography;
	DECLARE @areapoly nvarchar(MAX);
	SET @areapoly = 'POLYGON((:west :south, :east :south, :east :north, :west :north, :west :south))'
	SET @areapoly = REPLACE(@areapoly, ':north', CONVERT(nvarchar(MAX), @north))
	SET @areapoly = REPLACE(@areapoly, ':south', CONVERT(nvarchar(MAX), @south))
	SET @areapoly = REPLACE(@areapoly, ':east', CONVERT(nvarchar(MAX), @east))
	SET @areapoly = REPLACE(@areapoly, ':west', CONVERT(nvarchar(MAX), @west))
	SET @area = geography::STGeomFromText(@areapoly, 4326);

	CREATE TABLE #objectsr (
		ID BIGINT NOT NULL,
		ContextID BIGINT NOT NULL,
		URI NVARCHAR(MAX) NOT NULL,
		Locations NVARCHAR(MAX),
		Actions NVARCHAR(MAX),
		Metadata NVARCHAR(MAX),
		Pingbacks NVARCHAR(MAX));
	DECLARE @objectid BIGINT;
	DECLARE objectsc CURSOR FOR 
	SELECT
		DISTINCT l.ObjectID AS ObjectID
	FROM
		Location l, LocationPoint lp
	WHERE
		l.ID = lp.LocationID
		AND l.ContextID = @ContextID
		AND @area.STContains(lp.Center) = 1;
	OPEN objectsc;
	FETCH NEXT FROM objectsc INTO @objectid;
	WHILE @@FETCH_STATUS = 0
		BEGIN
			INSERT INTO #objectsr(ID, ContextID, URI, Locations, Actions, Metadata, Pingbacks) 
				SELECT o.ID, o.ContextID, o.URI,
					(SELECT 
							l.ID AS ID,
							l.Source AS Source,
							lp.CenterText AS CenterText,
							lp.Error AS Error
						FROM 
							Location l,
							LocationPoint lp
						WHERE 
							l.ObjectID = o.ID 
							AND l.ID = lp.LocationID
						FOR XML PATH ('Location'), ROOT ('Locations')) AS Locations,
					(SELECT 
							a.ID AS ID,
							a.URI AS URI,
							a.UserID AS UserID,
							a.[DateTime] AS [DateTime] 
						FROM [Action] a 
						WHERE a.ObjectID = o.ID 
						FOR XML PATH ('Action'), ROOT ('Actions')) AS Actions,
					(SELECT
							om.ID AS ID,
							om.Name AS Name,
							om.Value AS Value,
							om.[Type] AS [Type]
						FROM [ObjectMetadata] om
						WHERE om.ObjectID = o.ID
						FOR XML PATH ('Metadata'), ROOT('ObjectMetadata')) AS Metadata,
					(SELECT
							p.ID,
							p.[DateTime] AS [DateTime],
							p.URL AS URL
						FROM 
							Pingback p,
							PingbackObject po
						WHERE 
							p.ID = po.PingbackID
							AND po.ObjectID = o.ID
						FOR XML PATH ('Pingback'), ROOT('Pingbacks')) AS Pingbacks
				FROM ObjectOfInterest o 
				WHERE o.ID = @objectid;
			FETCH NEXT FROM objectsc INTO @objectid;
		END;
	CLOSE objectsc;
	DEALLOCATE objectsc;
	SELECT * FROM #objectsr;
	DROP TABLE #objectsr;
END


GO
/****** Object:  Table [dbo].[Action]    Script Date: 12/11/2012 11:53:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Action](
	[ID] [bigint] NOT NULL,
	[ContextID] [bigint] NOT NULL,
	[URI] [nvarchar](max) NOT NULL,
	[ObjectID] [bigint] NOT NULL,
	[UserID] [bigint] NOT NULL,
	[DateTime] [datetime2](7) NOT NULL,
 CONSTRAINT [PK_Action] PRIMARY KEY CLUSTERED 
(
	[ID] ASC,
	[ContextID] ASC
)WITH (STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF)
)FEDERATED ON (ContextID=ContextID)

GO
/****** Object:  Table [dbo].[ActionLocation]    Script Date: 12/11/2012 11:53:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[ActionLocation](
	[ID] [bigint] NOT NULL,
	[ContextID] [bigint] NOT NULL,
	[ActionID] [bigint] NOT NULL,
	[LocationID] [bigint] NOT NULL,
 CONSTRAINT [PK_ActionLocation] PRIMARY KEY CLUSTERED 
(
	[ID] ASC,
	[ContextID] ASC
)WITH (STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF)
)FEDERATED ON (ContextID=ContextID)

GO
/****** Object:  Table [dbo].[Context]    Script Date: 12/11/2012 11:53:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Context](
	[ID] [bigint] NOT NULL,
	[Name] [nvarchar](max) NOT NULL,
	[Key] [varbinary](max) NOT NULL,
 CONSTRAINT [PK_Context] PRIMARY KEY CLUSTERED 
(
	[ID] ASC
)WITH (STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF)
)FEDERATED ON (ContextID=ID)

GO
/****** Object:  Table [dbo].[Location]    Script Date: 12/11/2012 11:53:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Location](
	[ID] [bigint] NOT NULL,
	[ContextID] [bigint] NOT NULL,
	[Source] [smallint] NOT NULL,
	[ObjectID] [bigint] NOT NULL,
 CONSTRAINT [PK_Location] PRIMARY KEY CLUSTERED 
(
	[ID] ASC,
	[ContextID] ASC
)WITH (STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF)
)FEDERATED ON (ContextID=ContextID)

GO
/****** Object:  Table [dbo].[LocationEllipse]    Script Date: 12/11/2012 11:53:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[LocationEllipse](
	[ID] [bigint] NOT NULL,
	[ContextID] [bigint] NOT NULL,
	[Origin]  AS ([geography]::STGeomFromText([OriginText],(4326))) PERSISTED,
	[MajorAxis] [bigint] NOT NULL,
	[MinorAxis] [bigint] NOT NULL,
	[Angle] [float] NOT NULL,
	[LocationID] [bigint] NOT NULL,
	[OriginText] [nvarchar](max) NOT NULL,
 CONSTRAINT [PK_LocationEllipse] PRIMARY KEY CLUSTERED 
(
	[ID] ASC,
	[ContextID] ASC
)WITH (STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF)
)FEDERATED ON (ContextID=ContextID)

GO
/****** Object:  Table [dbo].[LocationNamed]    Script Date: 12/11/2012 11:53:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[LocationNamed](
	[ID] [bigint] NOT NULL,
	[ContextID] [bigint] NOT NULL,
	[LocationID] [bigint] NOT NULL,
 CONSTRAINT [PK_LocationNamed] PRIMARY KEY CLUSTERED 
(
	[ID] ASC,
	[ContextID] ASC
)WITH (STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF)
)FEDERATED ON (ContextID=ContextID)

GO
/****** Object:  Table [dbo].[LocationNamedPart]    Script Date: 12/11/2012 11:53:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[LocationNamedPart](
	[ID] [bigint] NOT NULL,
	[ContextID] [bigint] NOT NULL,
	[Type] [smallint] NOT NULL,
	[Part] [nvarchar](max) NOT NULL,
	[LocationNamedID] [bigint] NOT NULL,
	[Order] [smallint] NOT NULL,
 CONSTRAINT [PK_LocationNamedPart] PRIMARY KEY CLUSTERED 
(
	[ID] ASC,
	[ContextID] ASC
)WITH (STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF)
)FEDERATED ON (ContextID=ContextID)

GO
/****** Object:  Table [dbo].[LocationPoint]    Script Date: 12/11/2012 11:53:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[LocationPoint](
	[ID] [bigint] NOT NULL,
	[ContextID] [bigint] NOT NULL,
	[Center]  AS ([geography]::STGeomFromText([CenterText],(4326))) PERSISTED,
	[Error] [bigint] NOT NULL,
	[LocationID] [bigint] NOT NULL,
	[CenterText] [nvarchar](max) NOT NULL,
 CONSTRAINT [PK_LocationPoint] PRIMARY KEY CLUSTERED 
(
	[ID] ASC,
	[ContextID] ASC
)WITH (STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF)
)FEDERATED ON (ContextID=ContextID)

GO
/****** Object:  Table [dbo].[LocationPolygon]    Script Date: 12/11/2012 11:53:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[LocationPolygon](
	[ID] [bigint] NOT NULL,
	[ContextID] [bigint] NOT NULL,
	[Points]  AS ([geography]::STGeomFromText([PointsText],(4326))) PERSISTED,
	[LocationID] [bigint] NOT NULL,
	[PointsText] [nvarchar](max) NOT NULL,
 CONSTRAINT [PK_LocationPolygon] PRIMARY KEY CLUSTERED 
(
	[ID] ASC,
	[ContextID] ASC
)WITH (STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF)
)FEDERATED ON (ContextID=ContextID)

GO
/****** Object:  Table [dbo].[ObjectMetadata]    Script Date: 12/11/2012 11:53:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[ObjectMetadata](
	[ID] [bigint] NOT NULL,
	[ContextID] [bigint] NOT NULL,
	[ObjectID] [bigint] NOT NULL,
	[Name] [nvarchar](max) NOT NULL,
	[Value] [nvarchar](max) NOT NULL,
	[Type] [smallint] NOT NULL,
 CONSTRAINT [PK_ObjectMetadata] PRIMARY KEY CLUSTERED 
(
	[ID] ASC,
	[ContextID] ASC
)WITH (STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF)
)FEDERATED ON (ContextID=ContextID)

GO
/****** Object:  Table [dbo].[ObjectOfInterest]    Script Date: 12/11/2012 11:53:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[ObjectOfInterest](
	[ID] [bigint] NOT NULL,
	[ContextID] [bigint] NOT NULL,
	[URI] [nvarchar](max) NOT NULL,
 CONSTRAINT [PK_ObjectOfInterest] PRIMARY KEY CLUSTERED 
(
	[ID] ASC,
	[ContextID] ASC
)WITH (STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF)
)FEDERATED ON (ContextID=ContextID)

GO
/****** Object:  Table [dbo].[Pingback]    Script Date: 12/11/2012 11:53:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Pingback](
	[ID] [bigint] NOT NULL,
	[ContextID] [bigint] NOT NULL,
	[URL] [nvarchar](max) NOT NULL,
	[DateTime] [datetime2](7) NOT NULL,
 CONSTRAINT [PK_Pingback] PRIMARY KEY CLUSTERED 
(
	[ID] ASC,
	[ContextID] ASC
)WITH (STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF)
)FEDERATED ON (ContextID=ContextID)

GO
/****** Object:  Table [dbo].[PingbackAction]    Script Date: 12/11/2012 11:53:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[PingbackAction](
	[ID] [bigint] NOT NULL,
	[ContextID] [bigint] NOT NULL,
	[PingbackID] [bigint] NOT NULL,
	[ActionID] [bigint] NOT NULL,
 CONSTRAINT [PK_PingbackAction] PRIMARY KEY CLUSTERED 
(
	[ID] ASC,
	[ContextID] ASC
)WITH (STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF)
)FEDERATED ON (ContextID=ContextID)

GO
/****** Object:  Table [dbo].[PingbackLocation]    Script Date: 12/11/2012 11:53:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[PingbackLocation](
	[ID] [bigint] NOT NULL,
	[ContextID] [bigint] NOT NULL,
	[PingbackID] [bigint] NOT NULL,
	[LocationID] [bigint] NOT NULL,
 CONSTRAINT [PK_PingbackLocation] PRIMARY KEY CLUSTERED 
(
	[ID] ASC,
	[ContextID] ASC
)WITH (STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF)
)FEDERATED ON (ContextID=ContextID)

GO
/****** Object:  Table [dbo].[PingbackObject]    Script Date: 12/11/2012 11:53:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[PingbackObject](
	[ID] [bigint] NOT NULL,
	[ContextID] [bigint] NOT NULL,
	[PingbackID] [bigint] NOT NULL,
	[ObjectID] [bigint] NOT NULL,
 CONSTRAINT [PK_PingbackObject] PRIMARY KEY CLUSTERED 
(
	[ID] ASC,
	[ContextID] ASC
)WITH (STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF)
)FEDERATED ON (ContextID=ContextID)

GO
/****** Object:  Table [dbo].[PingbackUser]    Script Date: 12/11/2012 11:53:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[PingbackUser](
	[ID] [bigint] NOT NULL,
	[ContextID] [bigint] NOT NULL,
	[PingbackID] [bigint] NOT NULL,
	[UserID] [bigint] NOT NULL,
 CONSTRAINT [PK_PingbackUser] PRIMARY KEY CLUSTERED 
(
	[ID] ASC,
	[ContextID] ASC
)WITH (STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF)
)FEDERATED ON (ContextID=ContextID)

GO
/****** Object:  Table [dbo].[Sequence]    Script Date: 12/11/2012 11:53:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[Sequence](
	[TableName] [nchar](20) NOT NULL,
	[CurrentID] [bigint] NOT NULL,
 CONSTRAINT [PK_Sequence] PRIMARY KEY CLUSTERED 
(
	[TableName] ASC
)WITH (STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF)
)

GO
/****** Object:  Table [dbo].[User]    Script Date: 12/11/2012 11:53:15 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[User](
	[ID] [bigint] NOT NULL,
	[ContextID] [bigint] NOT NULL,
	[URI] [nvarchar](max) NOT NULL,
 CONSTRAINT [PK_User] PRIMARY KEY CLUSTERED 
(
	[ID] ASC,
	[ContextID] ASC
)WITH (STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF)
)FEDERATED ON (ContextID=ContextID)

GO
/****** Object:  Index [IX_Action_ObjectID]    Script Date: 12/11/2012 11:53:15 ******/
CREATE NONCLUSTERED INDEX [IX_Action_ObjectID] ON [dbo].[Action]
(
	[ObjectID] ASC
)WITH (STATISTICS_NORECOMPUTE = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
GO
/****** Object:  Index [IX_Location_ContextID]    Script Date: 12/11/2012 11:53:15 ******/
CREATE NONCLUSTERED INDEX [IX_Location_ContextID] ON [dbo].[Location]
(
	[ContextID] ASC
)WITH (STATISTICS_NORECOMPUTE = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
GO
/****** Object:  Index [IX_Location_ObjectID]    Script Date: 12/11/2012 11:53:15 ******/
CREATE NONCLUSTERED INDEX [IX_Location_ObjectID] ON [dbo].[Location]
(
	[ObjectID] ASC
)WITH (STATISTICS_NORECOMPUTE = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
GO
/****** Object:  Index [IX_LocationPoint_LocationID]    Script Date: 12/11/2012 11:53:15 ******/
CREATE NONCLUSTERED INDEX [IX_LocationPoint_LocationID] ON [dbo].[LocationPoint]
(
	[LocationID] ASC
)WITH (STATISTICS_NORECOMPUTE = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
GO
ALTER TABLE [dbo].[Location] ADD  CONSTRAINT [DF_Location_Source]  DEFAULT ((-1)) FOR [Source]
GO
ALTER TABLE [dbo].[LocationNamedPart] ADD  CONSTRAINT [DF_LocationNamedPart_Type]  DEFAULT ((-1)) FOR [Type]
GO
ALTER TABLE [dbo].[ObjectMetadata] ADD  CONSTRAINT [DF_ObjectMetadata_Type]  DEFAULT ((-1)) FOR [Type]
GO
ALTER TABLE [dbo].[Action]  WITH CHECK ADD  CONSTRAINT [FK_Action_ObjectOfInterest] FOREIGN KEY([ObjectID], [ContextID])
REFERENCES [dbo].[ObjectOfInterest] ([ID], [ContextID])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[Action] CHECK CONSTRAINT [FK_Action_ObjectOfInterest]
GO
ALTER TABLE [dbo].[Action]  WITH CHECK ADD  CONSTRAINT [FK_Action_User] FOREIGN KEY([UserID], [ContextID])
REFERENCES [dbo].[User] ([ID], [ContextID])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[Action] CHECK CONSTRAINT [FK_Action_User]
GO
ALTER TABLE [dbo].[ActionLocation]  WITH CHECK ADD  CONSTRAINT [FK_ActionLocation_Action] FOREIGN KEY([ActionID], [ContextID])
REFERENCES [dbo].[Action] ([ID], [ContextID])
GO
ALTER TABLE [dbo].[ActionLocation] CHECK CONSTRAINT [FK_ActionLocation_Action]
GO
ALTER TABLE [dbo].[ActionLocation]  WITH CHECK ADD  CONSTRAINT [FK_ActionLocation_Location] FOREIGN KEY([LocationID], [ContextID])
REFERENCES [dbo].[Location] ([ID], [ContextID])
GO
ALTER TABLE [dbo].[ActionLocation] CHECK CONSTRAINT [FK_ActionLocation_Location]
GO
ALTER TABLE [dbo].[Location]  WITH CHECK ADD  CONSTRAINT [FK_Location_ObjectOfInterest] FOREIGN KEY([ObjectID], [ContextID])
REFERENCES [dbo].[ObjectOfInterest] ([ID], [ContextID])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[Location] CHECK CONSTRAINT [FK_Location_ObjectOfInterest]
GO
ALTER TABLE [dbo].[LocationEllipse]  WITH CHECK ADD  CONSTRAINT [FK_LocationEllipse_Location] FOREIGN KEY([LocationID], [ContextID])
REFERENCES [dbo].[Location] ([ID], [ContextID])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[LocationEllipse] CHECK CONSTRAINT [FK_LocationEllipse_Location]
GO
ALTER TABLE [dbo].[LocationNamed]  WITH CHECK ADD  CONSTRAINT [FK_LocationNamed_Location] FOREIGN KEY([LocationID], [ContextID])
REFERENCES [dbo].[Location] ([ID], [ContextID])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[LocationNamed] CHECK CONSTRAINT [FK_LocationNamed_Location]
GO
ALTER TABLE [dbo].[LocationNamedPart]  WITH CHECK ADD  CONSTRAINT [FK_LocationNamedPart_LocationNamed] FOREIGN KEY([LocationNamedID], [ContextID])
REFERENCES [dbo].[LocationNamed] ([ID], [ContextID])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[LocationNamedPart] CHECK CONSTRAINT [FK_LocationNamedPart_LocationNamed]
GO
ALTER TABLE [dbo].[LocationPoint]  WITH CHECK ADD  CONSTRAINT [FK_LocationPoint_Location] FOREIGN KEY([LocationID], [ContextID])
REFERENCES [dbo].[Location] ([ID], [ContextID])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[LocationPoint] CHECK CONSTRAINT [FK_LocationPoint_Location]
GO
ALTER TABLE [dbo].[LocationPolygon]  WITH CHECK ADD  CONSTRAINT [FK_LocationPolygon_Location] FOREIGN KEY([LocationID], [ContextID])
REFERENCES [dbo].[Location] ([ID], [ContextID])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[LocationPolygon] CHECK CONSTRAINT [FK_LocationPolygon_Location]
GO
ALTER TABLE [dbo].[ObjectMetadata]  WITH CHECK ADD  CONSTRAINT [FK_ObjectMetadata_ObjectOfInterest] FOREIGN KEY([ObjectID], [ContextID])
REFERENCES [dbo].[ObjectOfInterest] ([ID], [ContextID])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[ObjectMetadata] CHECK CONSTRAINT [FK_ObjectMetadata_ObjectOfInterest]
GO
ALTER TABLE [dbo].[PingbackAction]  WITH CHECK ADD  CONSTRAINT [FK_PingbackAction_Action] FOREIGN KEY([ActionID], [ContextID])
REFERENCES [dbo].[Action] ([ID], [ContextID])
GO
ALTER TABLE [dbo].[PingbackAction] CHECK CONSTRAINT [FK_PingbackAction_Action]
GO
ALTER TABLE [dbo].[PingbackAction]  WITH CHECK ADD  CONSTRAINT [FK_PingbackAction_Pingback] FOREIGN KEY([PingbackID], [ContextID])
REFERENCES [dbo].[Pingback] ([ID], [ContextID])
GO
ALTER TABLE [dbo].[PingbackAction] CHECK CONSTRAINT [FK_PingbackAction_Pingback]
GO
ALTER TABLE [dbo].[PingbackLocation]  WITH CHECK ADD  CONSTRAINT [FK_PingbackLocation_Location] FOREIGN KEY([LocationID], [ContextID])
REFERENCES [dbo].[Location] ([ID], [ContextID])
GO
ALTER TABLE [dbo].[PingbackLocation] CHECK CONSTRAINT [FK_PingbackLocation_Location]
GO
ALTER TABLE [dbo].[PingbackLocation]  WITH CHECK ADD  CONSTRAINT [FK_PingbackLocation_Pingback] FOREIGN KEY([PingbackID], [ContextID])
REFERENCES [dbo].[Pingback] ([ID], [ContextID])
GO
ALTER TABLE [dbo].[PingbackLocation] CHECK CONSTRAINT [FK_PingbackLocation_Pingback]
GO
ALTER TABLE [dbo].[PingbackObject]  WITH CHECK ADD  CONSTRAINT [FK_PingbackObject_ObjectOfInterest] FOREIGN KEY([ObjectID], [ContextID])
REFERENCES [dbo].[ObjectOfInterest] ([ID], [ContextID])
GO
ALTER TABLE [dbo].[PingbackObject] CHECK CONSTRAINT [FK_PingbackObject_ObjectOfInterest]
GO
ALTER TABLE [dbo].[PingbackObject]  WITH CHECK ADD  CONSTRAINT [FK_PingbackObject_Pingback] FOREIGN KEY([PingbackID], [ContextID])
REFERENCES [dbo].[Pingback] ([ID], [ContextID])
GO
ALTER TABLE [dbo].[PingbackObject] CHECK CONSTRAINT [FK_PingbackObject_Pingback]
GO
ALTER TABLE [dbo].[PingbackUser]  WITH CHECK ADD  CONSTRAINT [FK_PingbackUser_Pingback] FOREIGN KEY([PingbackID], [ContextID])
REFERENCES [dbo].[Pingback] ([ID], [ContextID])
GO
ALTER TABLE [dbo].[PingbackUser] CHECK CONSTRAINT [FK_PingbackUser_Pingback]
GO
ALTER TABLE [dbo].[PingbackUser]  WITH CHECK ADD  CONSTRAINT [FK_PingbackUser_User] FOREIGN KEY([UserID], [ContextID])
REFERENCES [dbo].[User] ([ID], [ContextID])
GO
ALTER TABLE [dbo].[PingbackUser] CHECK CONSTRAINT [FK_PingbackUser_User]
GO
SET ARITHABORT ON
SET CONCAT_NULL_YIELDS_NULL ON
SET QUOTED_IDENTIFIER ON
SET ANSI_NULLS ON
SET ANSI_PADDING ON
SET ANSI_WARNINGS ON
SET NUMERIC_ROUNDABORT OFF

GO
/****** Object:  Index [SPATIAL_LocationEllipse]    Script Date: 12/11/2012 11:53:15 ******/
CREATE SPATIAL INDEX [SPATIAL_LocationEllipse] ON [dbo].[LocationEllipse]
(
	[Origin]
)USING  GEOGRAPHY_GRID 
WITH (GRIDS =(LEVEL_1 = MEDIUM,LEVEL_2 = MEDIUM,LEVEL_3 = MEDIUM,LEVEL_4 = MEDIUM), 
CELLS_PER_OBJECT = 16, STATISTICS_NORECOMPUTE = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
GO
SET ARITHABORT ON
SET CONCAT_NULL_YIELDS_NULL ON
SET QUOTED_IDENTIFIER ON
SET ANSI_NULLS ON
SET ANSI_PADDING ON
SET ANSI_WARNINGS ON
SET NUMERIC_ROUNDABORT OFF

GO
/****** Object:  Index [SPATIAL_LocationPoint]    Script Date: 12/11/2012 11:53:15 ******/
CREATE SPATIAL INDEX [SPATIAL_LocationPoint] ON [dbo].[LocationPoint]
(
	[Center]
)USING  GEOGRAPHY_GRID 
WITH (GRIDS =(LEVEL_1 = MEDIUM,LEVEL_2 = MEDIUM,LEVEL_3 = MEDIUM,LEVEL_4 = MEDIUM), 
CELLS_PER_OBJECT = 16, STATISTICS_NORECOMPUTE = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
GO
SET ARITHABORT ON
SET CONCAT_NULL_YIELDS_NULL ON
SET QUOTED_IDENTIFIER ON
SET ANSI_NULLS ON
SET ANSI_PADDING ON
SET ANSI_WARNINGS ON
SET NUMERIC_ROUNDABORT OFF

GO
/****** Object:  Index [SPATIAL_LocationPolygon]    Script Date: 12/11/2012 11:53:15 ******/
CREATE SPATIAL INDEX [SPATIAL_LocationPolygon] ON [dbo].[LocationPolygon]
(
	[Points]
)USING  GEOGRAPHY_GRID 
WITH (GRIDS =(LEVEL_1 = MEDIUM,LEVEL_2 = MEDIUM,LEVEL_3 = MEDIUM,LEVEL_4 = MEDIUM), 
CELLS_PER_OBJECT = 16, STATISTICS_NORECOMPUTE = OFF, DROP_EXISTING = OFF, ONLINE = OFF)
GO
