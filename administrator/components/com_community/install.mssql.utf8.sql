/****** Object:  Table [dbo].[#__community_wall]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_wall](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[contentid] [float] NOT NULL,
	[post_by] [float] NOT NULL,
	[ip] [varchar](45) NOT NULL,
	[comment] [text] NOT NULL,
	[date] [varchar](45) NOT NULL,
	[published] [tinyint] NOT NULL,
	[type] [varchar](200) NOT NULL,
 CONSTRAINT [PK_#__community_wall] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]

CREATE NONCLUSTERED INDEX [contentid] ON [dbo].[#__community_wall] 
(
	[contentid] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]

CREATE NONCLUSTERED INDEX [type] ON [dbo].[#__community_wall] 
(
	[type] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]

/****** Object:  Table [dbo].[#__community_videos_category]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_videos_category](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[parent] [int] NOT NULL,
	[name] [varchar](255) NOT NULL,
	[description] [text] NOT NULL,
	[published] [smallint] NOT NULL,
 CONSTRAINT [PK_#__community_videos_category] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]

/****** Object:  Table [dbo].[#__community_videos]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_videos](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[title] [varchar](200) NOT NULL,
	[type] [varchar](200) NOT NULL,
	[video_id] [varchar](200) NULL,
	[description] [text] NOT NULL,
	[creator] [float] NOT NULL,
	[creator_type] [varchar](200) NOT NULL,
	[created] [datetime] NULL,
	[permissions] [varchar](255) NOT NULL,
	[category_id] [float] NOT NULL,
	[hits] [float] NOT NULL,
	[published] [smallint] NOT NULL,
	[featured] [smallint] NOT NULL,
	[duration] [float] NULL,
	[status] [varchar](200) NOT NULL,
	[thumb] [varchar](255) NULL,
	[path] [varchar](255) NULL,
	[groupid] [float] NOT NULL,
	[filesize] [int] NOT NULL,
	[storage] [varchar](64) NOT NULL,
	[location] [text] NOT NULL,
	[latitude] [float] NOT NULL,
	[longitude] [float] NOT NULL,
 CONSTRAINT [PK_#__community_videos] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]

CREATE NONCLUSTERED INDEX [creator] ON [dbo].[#__community_videos] 
(
	[creator] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]

CREATE NONCLUSTERED INDEX [idx_groupid] ON [dbo].[#__community_videos] 
(
	[groupid] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]

CREATE NONCLUSTERED INDEX [idx_storage] ON [dbo].[#__community_videos] 
(
	[storage] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]

/****** Object:  Table [dbo].[#__community_users]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_users](
	[userid] [int] NOT NULL,
	[status] [text] NOT NULL,
	[status_access] [int] NOT NULL,
	[points] [int] NOT NULL,
	[posted_on] [datetime] NULL,
	[avatar] [text] NOT NULL,
	[thumb] [text] NOT NULL,
	[invite] [int] NOT NULL,
	[params] [text] NOT NULL,
	[views] [int] NOT NULL,
	[friends] [text] NOT NULL,
	[groups] [text] NOT NULL,
	[friendcount] [int] NOT NULL,
	[alias] [varchar](255) NOT NULL,
	[latitude] [float] NOT NULL,
	[longitude] [float] NOT NULL,
	[profile_id] [int] NOT NULL,
	[storage] [varchar](64) NOT NULL,
	[watermark_hash] [varchar](255) NOT NULL,
	[search_email] [smallint] NOT NULL,
 CONSTRAINT [PK_#__community_users] PRIMARY KEY CLUSTERED 
(         
	[userid] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_userpoints]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_userpoints](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[rule_name] [varchar](255) NOT NULL,
	[rule_description] [text] NOT NULL,
	[rule_plugin] [varchar](255) NOT NULL,
	[action_string] [varchar](255) NOT NULL,
	[component] [varchar](255) NOT NULL,
	[access] [smallint] NOT NULL,
	[points] [int] NOT NULL,
	[published] [smallint] NOT NULL,
	[system] [smallint] NOT NULL,
 CONSTRAINT [PK_#__community_userpoints] PRIMARY KEY CLUSTERED 
(         
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_user_status]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_user_status](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[userid] [int] NOT NULL,
	[status] [text] NOT NULL,
	[posted_on] [int] NOT NULL,
	[location] [text] NOT NULL,
	[latitude] [float] NOT NULL,
	[longitude] [float] NOT NULL,
 CONSTRAINT [PK_#__community_user_status] PRIMARY KEY CLUSTERED 
(         
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [userid] ON [dbo].[#__community_user_status] 
(         
	[userid] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_tags_words]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_tags_words](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[tag] [varchar](200) NOT NULL,
	[count] [int] NOT NULL,
	[modified] [datetime] NULL,
 CONSTRAINT [PK_#__community_tags_words] PRIMARY KEY CLUSTERED 
(         
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_tags]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_tags](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[element] [varchar](200) NOT NULL,
	[userid] [int] NOT NULL,
	[cid] [int] NOT NULL,
	[created] [datetime] NULL,
	[tag] [varchar](200) NOT NULL,
 CONSTRAINT [PK_#__community_tags] PRIMARY KEY CLUSTERED 
(         
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_storage_s3]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_storage_s3](
	[storageid] [varchar](255) NOT NULL,
	[resource_path] [varchar](255) NOT NULL
) ON [PRIMARY]
          
CREATE UNIQUE NONCLUSTERED INDEX [storageid] ON [dbo].[#__community_storage_s3] 
(         
	[storageid] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_reports_reporter]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_reports_reporter](
	[reportid] [int] NOT NULL,
	[message] [text] NOT NULL,
	[created_by] [int] NOT NULL,
	[created] [datetime] NULL,
	[ip] [varchar](255) NOT NULL
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_reports_actions]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_reports_actions](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[reportid] [int] NOT NULL,
	[label] [varchar](255) NOT NULL,
	[method] [varchar](255) NOT NULL,
	[parameters] [varchar](255) NOT NULL,
	[defaultaction] [smallint] NOT NULL,
 CONSTRAINT [PK_#__community_reports_actions] PRIMARY KEY CLUSTERED 
(         
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_reports]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_reports](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[uniquestring] [varchar](200) NOT NULL,
	[link] [varchar](255) NOT NULL,
	[status] [int] NOT NULL,
	[created] [datetime] NULL,
 CONSTRAINT [PK_#__community_reports] PRIMARY KEY CLUSTERED 
(         
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_register_auth_token]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_register_auth_token](
	[token] [varchar](200) NOT NULL,
	[ip] [varchar](25) NOT NULL,
	[auth_key] [varchar](200) NOT NULL,
	[created] [datetime] NULL,
 CONSTRAINT [PK_#__community_register_auth_token] PRIMARY KEY CLUSTERED 
(         
	[token] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [ip] ON [dbo].[#__community_register_auth_token] 
(         
	[ip] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_register]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_register](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[token] [varchar](200) NOT NULL,
	[name] [varchar](255) NOT NULL,
	[firstname] [varchar](180) NOT NULL,
	[lastname] [varchar](180) NOT NULL,
	[username] [varchar](150) NOT NULL,
	[email] [varchar](100) NOT NULL,
	[password] [varchar](100) NOT NULL,
	[created] [datetime] NULL,
	[ip] [varchar](25) NOT NULL,
 CONSTRAINT [PK_#__community_register] PRIMARY KEY CLUSTERED 
(         
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
          
          
/****** Object:  Table [dbo].[#__community_profiles_fields]    Script Date: 05/04/2011 12:56:12 ******/
          
CREATE TABLE [dbo].[#__community_profiles_fields](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[parent] [int] NOT NULL,
	[field_id] [int] NOT NULL,
 CONSTRAINT [PK_#__community_profiles_fields] PRIMARY KEY CLUSTERED 
(         
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [field_id] ON [dbo].[#__community_profiles_fields] 
(         
	[field_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [multiprofile_id] ON [dbo].[#__community_profiles_fields] 
(         
	[parent] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_profiles]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_profiles](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[name] [varchar](255) NOT NULL,
	[description] [text] NOT NULL,
	[approvals] [smallint] NOT NULL,
	[published] [smallint] NOT NULL,
	[avatar] [text] NOT NULL,
	[watermark] [text] NOT NULL,
	[watermark_hash] [varchar](255) NOT NULL,
	[watermark_location] [text] NOT NULL,
	[thumb] [text] NOT NULL,
	[created] [datetime] NULL,
	[create_groups] [smallint] NULL,
	[ordering] [int] NOT NULL,
 CONSTRAINT [PK_#__community_profiles] PRIMARY KEY CLUSTERED 
(         
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
          
          
CREATE NONCLUSTERED INDEX [approvals] ON [dbo].[#__community_profiles] 
(         
	[approvals] ASC,
	[published] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_photos_tokens]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_photos_tokens](
	[userid] [int] NOT NULL,
	[token] [varchar](200) NOT NULL,
	[datetime] [datetime] NULL
) ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_photos_tag]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_photos_tag](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[photoid] [int] NOT NULL,
	[userid] [int] NOT NULL,
	[position] [varchar](50) NOT NULL,
	[created_by] [int] NOT NULL,
	[created] [datetime] NULL,
 CONSTRAINT [PK_#__community_photos_tag] PRIMARY KEY CLUSTERED 
(         
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [idx_created_by] ON [dbo].[#__community_photos_tag] 
(         
	[created_by] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [idx_photo_user] ON [dbo].[#__community_photos_tag] 
(         
	[photoid] ASC,
	[userid] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [idx_photoid] ON [dbo].[#__community_photos_tag] 
(         
	[photoid] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [idx_userid] ON [dbo].[#__community_photos_tag] 
(         
	[userid] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_photos_albums]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_photos_albums](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[photoid] [int] NOT NULL,
	[creator] [int] NOT NULL,
	[name] [varchar](255) NOT NULL,
	[description] [text] NOT NULL,
	[permissions] [varchar](255) NOT NULL,
	[created] [datetime] NULL,
	[path] [varchar](255) NOT NULL,
	[type] [varchar](255) NOT NULL,
	[groupid] [int] NOT NULL,
	[hits] [int] NOT NULL,
	[location] [text] NOT NULL,
	[latitude] [float] NOT NULL,
	[longitude] [float] NOT NULL,
	[default] [smallint] NOT NULL,
 CONSTRAINT [PK_#__community_photos_albums] PRIMARY KEY CLUSTERED 
(         
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [creator] ON [dbo].[#__community_photos_albums] 
(         
	[creator] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [idx_albumtype] ON [dbo].[#__community_photos_albums] 
(         
	[id] ASC,
	[type] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [idx_creatortype] ON [dbo].[#__community_photos_albums] 
(         
	[creator] ASC,
	[type] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [idx_groupid] ON [dbo].[#__community_photos_albums] 
(         
	[groupid] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [idx_type] ON [dbo].[#__community_photos_albums] 
(         
	[type] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_photos]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_photos](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[albumid] [int] NOT NULL,
	[caption] [text] NOT NULL,
	[published] [smallint] NOT NULL,
	[creator] [int] NOT NULL,
	[permissions] [varchar](255) NOT NULL,
	[image] [varchar](255) NOT NULL,
	[thumbnail] [varchar](255) NOT NULL,
	[original] [varchar](255) NOT NULL,
	[filesize] [int] NOT NULL,
	[storage] [varchar](64) NOT NULL,
	[created] [datetime] NULL,
	[ordering] [int] NOT NULL,
	[hits] [int] NOT NULL,
	[status] [varchar](200) NOT NULL,
 CONSTRAINT [PK_#__community_photos] PRIMARY KEY CLUSTERED 
(         
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [albumid] ON [dbo].[#__community_photos] 
(         
	[albumid] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [idx_storage] ON [dbo].[#__community_photos] 
(         
	[storage] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_oauth]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_oauth](
	[userid] [int] NOT NULL,
	[requesttoken] [text] NOT NULL,
	[accesstoken] [text] NOT NULL,
	[app] [varchar](255) NOT NULL,
 CONSTRAINT [PK_#__community_oauth] PRIMARY KEY CLUSTERED 
(         
	[userid] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_msg_recepient]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_msg_recepient](
	[msg_id] [float] NOT NULL,
	[msg_parent] [float] NOT NULL,
	[msg_from] [float] NOT NULL,
	[to] [float] NOT NULL,
	[bcc] [tinyint] NULL,
	[is_read] [tinyint] NULL,
	[deleted] [tinyint] NULL
) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [idx_isread_to_deleted] ON [dbo].[#__community_msg_recepient] 
(         
	[is_read] ASC,
	[to] ASC,
	[deleted] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [msg_id] ON [dbo].[#__community_msg_recepient] 
(         
	[msg_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [to] ON [dbo].[#__community_msg_recepient] 
(         
	[to] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
CREATE UNIQUE NONCLUSTERED INDEX [un] ON [dbo].[#__community_msg_recepient] 
(         
	[msg_id] ASC,
	[to] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_msg]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_msg](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[from] [float] NOT NULL,
	[parent] [float] NOT NULL,
	[deleted] [tinyint] NULL,
	[from_name] [varchar](45) NOT NULL,
	[posted_on] [datetime] NULL,
	[subject] [varchar](765) NOT NULL,
	[body] [text] NOT NULL,
 CONSTRAINT [PK_#__community_msg] PRIMARY KEY CLUSTERED 
(         
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_memberlist_criteria]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_memberlist_criteria](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[listid] [int] NOT NULL,
	[field] [varchar](255) NOT NULL,
	[condition] [varchar](255) NOT NULL,
	[value] [varchar](255) NOT NULL,
	[type] [varchar](255) NOT NULL,
 CONSTRAINT [PK_#__community_memberlist_criteria] PRIMARY KEY CLUSTERED 
(         
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [listid] ON [dbo].[#__community_memberlist_criteria] 
(         
	[listid] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_memberlist]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_memberlist](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[title] [text] NOT NULL,
	[description] [text] NOT NULL,
	[condition] [varchar](255) NOT NULL,
	[avataronly] [int] NOT NULL,
	[created] [datetime] NULL,
 CONSTRAINT [PK_#__community_memberlist] PRIMARY KEY CLUSTERED 
(         
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_mailq]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_mailq](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[recipient] [text] NOT NULL,
	[subject] [text] NOT NULL,
	[body] [text] NOT NULL,
	[status] [smallint] NOT NULL,
	[created] [datetime] NULL,
	[template] [varchar](64) NOT NULL,
	[params] [text] NOT NULL,
 CONSTRAINT [PK_#__community_mailq] PRIMARY KEY CLUSTERED 
(         
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [status] ON [dbo].[#__community_mailq] 
(         
	[status] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_location_cache]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_location_cache](
	[address] [varchar](255) NOT NULL,
	[latitude] [float] NOT NULL,
	[longitude] [float] NOT NULL,
	[data] [text] NOT NULL,
	[status] [varchar](2) NOT NULL,
	[created] [datetime] NULL
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_likes]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_likes](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[element] [varchar](200) NOT NULL,
	[uid] [int] NOT NULL,
	[like] [text] NOT NULL,
	[dislike] [text] NOT NULL,
 CONSTRAINT [PK_#__community_likes] PRIMARY KEY CLUSTERED 
(         
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [element] ON [dbo].[#__community_likes] 
(         
	[element] ASC,
	[uid] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_invitations]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_invitations](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[callback] [varchar](255) NOT NULL,
	[cid] [int] NOT NULL,
	[users] [text] NOT NULL,
 CONSTRAINT [PK_#__community_invitations] PRIMARY KEY CLUSTERED 
(         
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_groups_members]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_groups_members](
	[groupid] [int] NOT NULL,
	[memberid] [int] NOT NULL,
	[approved] [int] NOT NULL,
	[permissions] [int] NOT NULL
) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [groupid] ON [dbo].[#__community_groups_members] 
(         
	[groupid] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [idx_memberid] ON [dbo].[#__community_groups_members] 
(         
	[memberid] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_groups_invite]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_groups_invite](
	[groupid] [int] NOT NULL,
	[userid] [int] NOT NULL,
	[creator] [int] NOT NULL
) ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_groups_discuss]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_groups_discuss](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[parentid] [int] NOT NULL,
	[groupid] [int] NOT NULL,
	[creator] [int] NOT NULL,
	[created] [datetime] NULL,
	[title] [text] NOT NULL,
	[message] [text] NOT NULL,
	[lastreplied] [datetime] NULL,
	[lock] [smallint] NOT NULL,
 CONSTRAINT [PK_#__community_groups_discuss] PRIMARY KEY CLUSTERED 
(         
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [groupid] ON [dbo].[#__community_groups_discuss] 
(         
	[groupid] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [parentid] ON [dbo].[#__community_groups_discuss] 
(         
	[parentid] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_groups_category]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_groups_category](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[parent] [int] NOT NULL,
	[name] [varchar](255) NOT NULL,
	[description] [text] NOT NULL,
 CONSTRAINT [PK_#__community_groups_category] PRIMARY KEY CLUSTERED 
(         
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_groups_bulletins]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_groups_bulletins](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[groupid] [int] NOT NULL,
	[created_by] [int] NOT NULL,
	[published] [smallint] NOT NULL,
	[title] [varchar](255) NOT NULL,
	[message] [text] NOT NULL,
	[date] [datetime] NULL,
 CONSTRAINT [PK_#__community_groups_bulletins] PRIMARY KEY CLUSTERED 
(         
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [groupid] ON [dbo].[#__community_groups_bulletins] 
(         
	[groupid] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_groups]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_groups](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[published] [smallint] NOT NULL,
	[ownerid] [int] NOT NULL,
	[categoryid] [int] NOT NULL,
	[name] [varchar](255) NOT NULL,
	[description] [text] NOT NULL,
	[email] [varchar](255) NOT NULL,
	[website] [varchar](255) NOT NULL,
	[approvals] [smallint] NOT NULL,
	[created] [datetime] NULL,
	[avatar] [text] NOT NULL,
	[thumb] [text] NOT NULL,
	[discusscount] [int] NOT NULL,
	[wallcount] [int] NOT NULL,
	[membercount] [int] NOT NULL,
	[params] [text] NOT NULL,
	[storage] [varchar](64) NOT NULL,
 CONSTRAINT [PK_#__community_groups] PRIMARY KEY CLUSTERED 
(         
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_fields_values]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_fields_values](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[user_id] [int] NOT NULL,
	[field_id] [int] NOT NULL,
	[value] [text] NOT NULL,
	[access] [smallint] NOT NULL,
 CONSTRAINT [PK_#__community_fields_values] PRIMARY KEY CLUSTERED 
(         
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [access] ON [dbo].[#__community_fields_values] 
(         
	[access] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [field_id] ON [dbo].[#__community_fields_values] 
(         
	[field_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [idx_user_fieldid] ON [dbo].[#__community_fields_values] 
(         
	[user_id] ASC,
	[field_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [user_id] ON [dbo].[#__community_fields_values] 
(         
	[user_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_fields]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_fields](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[type] [varchar](255) NOT NULL,
	[ordering] [int] NULL,
	[published] [smallint] NOT NULL,
	[min] [int] NOT NULL,
	[max] [int] NOT NULL,
	[name] [varchar](255) NOT NULL,
	[tips] [text] NOT NULL,
	[visible] [smallint] NULL,
	[required] [smallint] NULL,
	[searchable] [smallint] NULL,
	[registration] [smallint] NULL,
	[options] [text] NULL,
	[fieldcode] [varchar](255) NOT NULL,
	[params] [text] NOT NULL,
 CONSTRAINT [PK_#__community_fields] PRIMARY KEY CLUSTERED 
(         
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [fieldcode] ON [dbo].[#__community_fields] 
(         
	[fieldcode] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_featured]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_featured](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[cid] [int] NOT NULL,
	[created_by] [int] NOT NULL,
	[type] [varchar](255) NOT NULL,
	[created] [datetime] NULL,
 CONSTRAINT [PK_#__community_featured] PRIMARY KEY CLUSTERED 
(         
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [type] ON [dbo].[#__community_featured] 
(         
	[type] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_events_members]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_events_members](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[eventid] [float] NOT NULL,
	[memberid] [float] NOT NULL,
	[status] [tinyint] NOT NULL,
	[permission] [tinyint] NOT NULL,
	[invited_by] [float] NULL,
	[approval] [tinyint] NULL,
	[created] [datetime] NULL,
 CONSTRAINT [PK_#__community_events_members] PRIMARY KEY CLUSTERED 
(         
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [idx_eventid] ON [dbo].[#__community_events_members] 
(         
	[eventid] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [idx_invitedby] ON [dbo].[#__community_events_members] 
(         
	[invited_by] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [idx_member_event] ON [dbo].[#__community_events_members] 
(         
	[eventid] ASC,
	[memberid] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [idx_permission] ON [dbo].[#__community_events_members] 
(         
	[eventid] ASC,
	[permission] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [idx_status] ON [dbo].[#__community_events_members] 
(         
	[status] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_events_category]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_events_category](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[parent] [int] NOT NULL,
	[name] [varchar](255) NOT NULL,
	[description] [text] NOT NULL,
 CONSTRAINT [PK_#__community_events_category] PRIMARY KEY CLUSTERED 
(         
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_events]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_events](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[catid] [float] NOT NULL,
	[contentid] [float] NULL,
	[type] [varchar](255) NOT NULL,
	[title] [varchar](255) NOT NULL,
	[location] [text] NOT NULL,
	[description] [text] NULL,
	[creator] [float] NOT NULL,
	[startdate] [datetime] NULL,
	[enddate] [datetime] NULL,
	[permission] [tinyint] NOT NULL,
	[avatar] [varchar](255) NULL,
	[thumb] [varchar](255) NULL,
	[invitedcount] [float] NULL,
	[confirmedcount] [float] NULL,
	[declinedcount] [float] NULL,
	[maybecount] [float] NULL,
	[wallcount] [float] NULL,
	[ticket] [float] NULL,
	[allowinvite] [tinyint] NULL,
	[created] [datetime] NULL,
	[hits] [float] NULL,
	[published] [float] NULL,
	[latitude] [float] NOT NULL,
	[longitude] [float] NOT NULL,
	[offset] [varchar](5) NULL,
 CONSTRAINT [PK_#__community_events] PRIMARY KEY CLUSTERED 
(         
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [idx_creator] ON [dbo].[#__community_events] 
(         
	[creator] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [idx_period] ON [dbo].[#__community_events] 
(         
	[startdate] ASC,
	[enddate] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [idx_type] ON [dbo].[#__community_events] 
(         
	[type] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_connection]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_connection](
	[connection_id] [int] IDENTITY(1,1) NOT NULL,
	[connect_from] [int] NOT NULL,
	[connect_to] [int] NOT NULL,
	[status] [smallint] NOT NULL,
	[group] [int] NOT NULL,
	[msg] [text] NOT NULL,
	[created] [datetime] NULL,
 CONSTRAINT [PK_#__community_connection] PRIMARY KEY CLUSTERED 
(         
	[connection_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [connect_from] ON [dbo].[#__community_connection] 
(         
	[connect_from] ASC,
	[connect_to] ASC,
	[status] ASC,
	[group] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [idx_connect_from] ON [dbo].[#__community_connection] 
(         
	[connect_from] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [idx_connect_to] ON [dbo].[#__community_connection] 
(         
	[connect_to] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [idx_connect_tofrom] ON [dbo].[#__community_connection] 
(         
	[connect_to] ASC,
	[connect_from] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_connect_users]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_connect_users](
	[connectid] [float] NOT NULL,
	[type] [varchar](255) NOT NULL,
	[userid] [int] NOT NULL
) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [type] ON [dbo].[#__community_connect_users] 
(         
	[type] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_config]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_config](
	[name] [varchar](64) NOT NULL,
	[params] [text] NOT NULL
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_blocklist]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_blocklist](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[userid] [int] NOT NULL,
	[blocked_userid] [int] NOT NULL,
 CONSTRAINT [PK_#__community_blocklist] PRIMARY KEY CLUSTERED 
(         
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [blocked_userid] ON [dbo].[#__community_blocklist] 
(         
	[blocked_userid] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [userid] ON [dbo].[#__community_blocklist] 
(         
	[userid] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_avatar]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_avatar](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[apptype] [varchar](255) NOT NULL,
	[path] [text] NOT NULL,
	[type] [tinyint] NOT NULL
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
          
CREATE UNIQUE NONCLUSTERED INDEX [id] ON [dbo].[#__community_avatar] 
(         
	[id] ASC,
	[apptype] ASC,
	[type] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_apps]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_apps](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[userid] [float] NOT NULL,
	[apps] [varchar](200) NOT NULL,
	[ordering] [float] NOT NULL,
	[position] [varchar](50) NOT NULL,
	[params] [text] NOT NULL,
	[privacy] [varchar](200) NOT NULL,
 CONSTRAINT [PK_#__community_apps] PRIMARY KEY CLUSTERED 
(         
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [idx_user_apps] ON [dbo].[#__community_apps] 
(         
	[userid] ASC,
	[apps] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [idx_userid] ON [dbo].[#__community_apps] 
(         
	[userid] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_activities_hide]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_activities_hide](
	[activity_id] [int] NOT NULL,
	[user_id] [int] NOT NULL
) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [user_id] ON [dbo].[#__community_activities_hide] 
(         
	[user_id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
/****** Object:  Table [dbo].[#__community_activities]    Script Date: 05/04/2011 12:56:12 ******/
CREATE TABLE [dbo].[#__community_activities](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[actor] [float] NOT NULL,
	[target] [float] NOT NULL,
	[title] [text] NOT NULL,
	[content] [text] NOT NULL,
	[app] [varchar](200) NOT NULL,
	[cid] [int] NOT NULL,
	[created] [datetime] NULL,
	[access] [tinyint] NOT NULL,
	[params] [text] NOT NULL,
	[points] [int] NOT NULL,
	[archived] [smallint] NOT NULL,
	[location] [text] NOT NULL,
	[latitude] [float] NOT NULL,
	[longitude] [float] NOT NULL,
	[comment_id] [int] NOT NULL,
	[comment_type] [varchar](200) NOT NULL,
	[like_id] [int] NOT NULL,
	[like_type] [varchar](200) NOT NULL,
 CONSTRAINT [PK_#__community_activities] PRIMARY KEY CLUSTERED 
(         
	[id] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [actor] ON [dbo].[#__community_activities] 
(         
	[actor] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [app] ON [dbo].[#__community_activities] 
(         
	[app] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [archived] ON [dbo].[#__community_activities] 
(         
	[archived] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [created] ON [dbo].[#__community_activities] 
(         
	[created] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
CREATE NONCLUSTERED INDEX [target] ON [dbo].[#__community_activities] 
(         
	[target] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, DROP_EXISTING = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]
          
/****** Object:  Default [DF__#__commu__conte__1EA48E88]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_wall] ADD  DEFAULT ((0)) FOR [contentid]
          
/****** Object:  Default [DF__#__commu__post___1F98B2C1]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_wall] ADD  DEFAULT ((0)) FOR [post_by]
          
/****** Object:  Default [DF__#__commu__publi__1BC821DD]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_videos_category] ADD  DEFAULT ((1)) FOR [published]
          
/****** Object:  Default [DF__#__commun__type__0D7A0286]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_videos] ADD  DEFAULT ('file') FOR [type]
          
/****** Object:  Default [DF__#__commu__creat__0E6E26BF]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_videos] ADD  DEFAULT ('user') FOR [creator_type]
          
/****** Object:  Default [DF__#__commu__permi__0F624AF8]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_videos] ADD  DEFAULT ('0') FOR [permissions]
          
/****** Object:  Default [DF__#__commun__hits__10566F31]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_videos] ADD  DEFAULT ((0)) FOR [hits]
          
/****** Object:  Default [DF__#__commu__publi__114A936A]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_videos] ADD  DEFAULT ((1)) FOR [published]
          
/****** Object:  Default [DF__#__commu__featu__123EB7A3]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_videos] ADD  DEFAULT ((0)) FOR [featured]
          
/****** Object:  Default [DF__#__commu__durat__1332DBDC]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_videos] ADD  DEFAULT ((0)) FOR [duration]
          
/****** Object:  Default [DF__#__commu__statu__14270015]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_videos] ADD  DEFAULT ('pending') FOR [status]
          
/****** Object:  Default [DF__#__commu__group__151B244E]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_videos] ADD  DEFAULT ((0)) FOR [groupid]
          
/****** Object:  Default [DF__#__commu__files__160F4887]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_videos] ADD  DEFAULT ((0)) FOR [filesize]
          
/****** Object:  Default [DF__#__commu__stora__17036CC0]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_videos] ADD  DEFAULT ('file') FOR [storage]
          
/****** Object:  Default [DF__#__commu__latit__17F790F9]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_videos] ADD  DEFAULT ((255)) FOR [latitude]
          
/****** Object:  Default [DF__#__commu__longi__18EBB532]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_videos] ADD  DEFAULT ((255)) FOR [longitude]
          
/****** Object:  Default [DF__#__commu__statu__02FC7413]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_users] ADD  DEFAULT ((0)) FOR [status_access]
          
/****** Object:  Default [DF__#__commu__invit__03F0984C]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_users] ADD  DEFAULT ((0)) FOR [invite]
          
/****** Object:  Default [DF__#__commun__view__04E4BC85]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_users] ADD  DEFAULT ((0)) FOR [view]
          
/****** Object:  Default [DF__#__commu__frien__05D8E0BE]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_users] ADD  DEFAULT ((0)) FOR [friendcount]
          
/****** Object:  Default [DF__#__commu__latit__06CD04F7]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_users] ADD  DEFAULT ((255)) FOR [latitude]
          
/****** Object:  Default [DF__#__commu__longi__07C12930]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_users] ADD  DEFAULT ((255)) FOR [longitude]
          
/****** Object:  Default [DF__#__commu__profi__08B54D69]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_users] ADD  DEFAULT ((0)) FOR [profile_id]
          
/****** Object:  Default [DF__#__commu__stora__09A971A2]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_users] ADD  DEFAULT ('file') FOR [storage]
          
/****** Object:  Default [DF__#__commu__searc__0A9D95DB]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_users] ADD  DEFAULT ((1)) FOR [search_email]
          
/****** Object:  Default [DF__#__commu__acces__7D439ABD]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_userpoints] ADD  DEFAULT ((1)) FOR [access]
          
/****** Object:  Default [DF__#__commu__point__7E37BEF6]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_userpoints] ADD  DEFAULT ((0)) FOR [points]
          
/****** Object:  Default [DF__#__commu__publi__7F2BE32F]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_userpoints] ADD  DEFAULT ((0)) FOR [published]
          
/****** Object:  Default [DF__#__commu__syste__00200768]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_userpoints] ADD  DEFAULT ((0)) FOR [system]
          
/****** Object:  Default [DF__#__commu__latit__797309D9]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_user_status] ADD  DEFAULT ((255)) FOR [latitude]
          
/****** Object:  Default [DF__#__commu__longi__7A672E12]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_user_status] ADD  DEFAULT ((255)) FOR [longitude]
          
/****** Object:  Default [DF__#__commu__creat__6754599E]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_profiles] ADD  DEFAULT ((1)) FOR [create_groups]
          
/****** Object:  Default [DF__#__commu__group__5DCAEF64]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_photos_albums] ADD  DEFAULT ((0)) FOR [groupid]
          
/****** Object:  Default [DF__#__commun__hits__5EBF139D]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_photos_albums] ADD  DEFAULT ((0)) FOR [hits]
          
/****** Object:  Default [DF__#__commu__latit__5FB337D6]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_photos_albums] ADD  DEFAULT ((255)) FOR [latitude]
          
/****** Object:  Default [DF__#__commu__longi__60A75C0F]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_photos_albums] ADD  DEFAULT ((255)) FOR [longitude]
          
/****** Object:  Default [DF__#__commu__defau__619B8048]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_photos_albums] ADD  DEFAULT ((0)) FOR [default]
          
/****** Object:  Default [DF__#__commu__files__5812160E]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_photos] ADD  DEFAULT ((0)) FOR [filesize]
          
/****** Object:  Default [DF__#__commu__stora__59063A47]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_photos] ADD  DEFAULT ('file') FOR [storage]
          
/****** Object:  Default [DF__#__commu__order__59FA5E80]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_photos] ADD  DEFAULT ((0)) FOR [ordering]
          
/****** Object:  Default [DF__#__commun__hits__5AEE82B9]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_photos] ADD  DEFAULT ((0)) FOR [hits]
          
/****** Object:  Default [DF__#__commu__msg_p__5165187F]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_msg_recepient] ADD  DEFAULT ((0)) FOR [msg_parent]
          
/****** Object:  Default [DF__#__communi__bcc__52593CB8]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_msg_recepient] ADD  DEFAULT ((0)) FOR [bcc]
          
/****** Object:  Default [DF__#__commu__is_re__534D60F1]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_msg_recepient] ADD  DEFAULT ((0)) FOR [is_read]
          
/****** Object:  Default [DF__#__commu__delet__5441852A]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_msg_recepient] ADD  DEFAULT ((0)) FOR [deleted]
          
/****** Object:  Default [DF__#__commu__delet__4E88ABD4]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_msg] ADD  DEFAULT ((0)) FOR [deleted]
          
/****** Object:  Default [DF__#__commu__latit__45F365D3]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_location_cache] ADD  DEFAULT ((255)) FOR [latitude]
          
/****** Object:  Default [DF__#__commu__longi__46E78A0C]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_location_cache] ADD  DEFAULT ((255)) FOR [longitude]
          
/****** Object:  Default [DF__#__commu__paren__3C69FB99]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_groups_discuss] ADD  DEFAULT ((0)) FOR [parentid]
          
/****** Object:  Default [DF__#__commun__lock__3D5E1FD2]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_groups_discuss] ADD  DEFAULT ((0)) FOR [lock]
          
/****** Object:  Default [DF__#__commu__paren__398D8EEE]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_groups_category] ADD  DEFAULT ((0)) FOR [parent]
          
/****** Object:  Default [DF__#__commu__discu__31EC6D26]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_groups] ADD  DEFAULT ((0)) FOR [discusscount]
          
/****** Object:  Default [DF__#__commu__wallc__32E0915F]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_groups] ADD  DEFAULT ((0)) FOR [wallcount]
          
/****** Object:  Default [DF__#__commu__membe__33D4B598]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_groups] ADD  DEFAULT ((0)) FOR [membercount]
          
/****** Object:  Default [DF__#__commu__stora__34C8D9D1]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_groups] ADD  DEFAULT ('file') FOR [storage]
          
/****** Object:  Default [DF__#__commu__acces__2F10007B]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_fields_values] ADD  DEFAULT ((0)) FOR [access]
          
/****** Object:  Default [DF__#__commu__order__276EDEB3]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_fields] ADD  DEFAULT ((0)) FOR [ordering]
          
/****** Object:  Default [DF__#__commu__publi__286302EC]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_fields] ADD  DEFAULT ((0)) FOR [published]
          
/****** Object:  Default [DF__#__commu__visib__29572725]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_fields] ADD  DEFAULT ((0)) FOR [visible]
          
/****** Object:  Default [DF__#__commu__requi__2A4B4B5E]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_fields] ADD  DEFAULT ((0)) FOR [required]
          
/****** Object:  Default [DF__#__commu__searc__2B3F6F97]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_fields] ADD  DEFAULT ((1)) FOR [searchable]
          
/****** Object:  Default [DF__#__commu__regis__2C3393D0]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_fields] ADD  DEFAULT ((1)) FOR [registration]
          
/****** Object:  Default [DF__#__commu__statu__1FCDBCEB]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_events_members] ADD  DEFAULT ((0)) FOR [status]
          
/****** Object:  Default [DF__#__commu__permi__20C1E124]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_events_members] ADD  DEFAULT ((3)) FOR [permission]
          
/****** Object:  Default [DF__#__commu__invit__21B6055D]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_events_members] ADD  DEFAULT ((0)) FOR [invited_by]
          
/****** Object:  Default [DF__#__commu__appro__22AA2996]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_events_members] ADD  DEFAULT ((0)) FOR [approval]
          
/****** Object:  Default [DF__#__commu__paren__1CF15040]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_events_category] ADD  DEFAULT ((0)) FOR [parent]
          
/****** Object:  Default [DF__#__commu__conte__0DAF0CB0]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_events] ADD  DEFAULT ((0)) FOR [contentid]
          
/****** Object:  Default [DF__#__commun__type__0EA330E9]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_events] ADD  DEFAULT ('profile') FOR [type]
          
/****** Object:  Default [DF__#__commu__permi__0F975522]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_events] ADD  DEFAULT ((0)) FOR [permission]
          
/****** Object:  Default [DF__#__commu__invit__108B795B]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_events] ADD  DEFAULT ((0)) FOR [invitedcount]
          
/****** Object:  Default [DF__#__commu__confi__117F9D94]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_events] ADD  DEFAULT ((0)) FOR [confirmedcount]
          
/****** Object:  Default [DF__#__commu__decli__1273C1CD]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_events] ADD  DEFAULT ((0)) FOR [declinedcount]
          
/****** Object:  Default [DF__#__commu__maybe__1367E606]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_events] ADD  DEFAULT ((0)) FOR [maybecount]
          
/****** Object:  Default [DF__#__commu__wallc__145C0A3F]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_events] ADD  DEFAULT ((0)) FOR [wallcount]
          
/****** Object:  Default [DF__#__commu__ticke__15502E78]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_events] ADD  DEFAULT ((0)) FOR [ticket]
          
/****** Object:  Default [DF__#__commu__allow__164452B1]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_events] ADD  DEFAULT ((1)) FOR [allowinvite]
          
/****** Object:  Default [DF__#__commun__hits__173876EA]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_events] ADD  DEFAULT ((0)) FOR [hits]
          
/****** Object:  Default [DF__#__commu__publi__182C9B23]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_events] ADD  DEFAULT ((1)) FOR [published]
          
/****** Object:  Default [DF__#__commu__latit__1920BF5C]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_events] ADD  DEFAULT ((255)) FOR [latitude]
          
/****** Object:  Default [DF__#__commu__longi__1A14E395]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_events] ADD  DEFAULT ((255)) FOR [longitude]
          
/****** Object:  Default [DF__#__commu__posit__0425A276]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_apps] ADD  DEFAULT ('content') FOR [position]
          
/****** Object:  Default [DF__#__commu__point__7E6CC920]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_activities] ADD  DEFAULT ((1)) FOR [points]
          
/****** Object:  Default [DF__#__commu__latit__7F60ED59]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_activities] ADD  DEFAULT ((255)) FOR [latitude]
          
/****** Object:  Default [DF__#__commu__longi__00551192]    Script Date: 05/04/2011 12:56:12 ******/
ALTER TABLE [dbo].[#__community_activities] ADD  DEFAULT ((255)) FOR [longitude]
          
ALTER TABLE [dbo].[#__community_fields] ADD  DEFAULT ('') FOR [params]
          
ALTER TABLE [dbo].[#__menu] ADD  DEFAULT (N'') FOR [img]
          
ALTER TABLE [dbo].[#__menu] ADD  DEFAULT (N'') FOR [params]