//
//  RestAPIConnector.m
//  WordPress
//
//  Created by Shakir Ali on 18/07/2012.
//  Copyright (c) 2012 WordPress. All rights reserved.
//

#import "RestAPIConnector.h"
#import "BlogExperience.h"
#import "ExperienceConfigurer.h"

static RestAPIConnector *sharedInstance = nil;

@implementation RestAPIConnector

NSString* const SERVER_CONTEXT_URL_FORMAT = @"http://artmapscore.cloudapp.net/service/%@/rest/v1";
NSString* const SERVICES_CONTEXT = @"tate";
NSString* const SEARCH_URL_FORMAT = @"objectsofinterest/search/?boundingbox.northEast.latitude=%@&boundingbox.southWest.latitude=%@&boundingbox.northEast.longitude=%@&boundingbox.southWest.longitude=%@";
NSString* const METADATA_URL_FORMAT = @"objectsofinterest/%@/metadata";
NSString* const EMBEDDED_MAP_URL_FORMAT = @"http://%@.artmaps.wp.horizon.ac.uk/wp-content/plugins/artmaps/php/artmapsembed.php";
NSString* const EMBEDDED_MAP_PARAMETER_FORMAT = @"c=%@";
NSString* const EMBEDDED_MAP_PARAMETER_VALUE_JSON_FORMAT=@"{ \"map\":{ \"center\" : { \"latitude\":%@, \"longitude\":%@, \"zoom\":%d}}}"; 

+(RestAPIConnector*)sharedInstance{
    if (sharedInstance){
        return sharedInstance;
    }
    @synchronized(self)
    {
        if (sharedInstance == nil ){
            sharedInstance = [[RestAPIConnector alloc] init];
        }
    }
    return sharedInstance;
}

-(void)dealloc{
    [sharedInstance release];
    [super dealloc];
}

+(id)allocWithZone:(NSZone*)zone
{
	@synchronized(self) {
        if (sharedInstance == nil) {
            sharedInstance = [super allocWithZone:zone];
            return sharedInstance;  // assignment and return on first allocation
        }
    }
    return nil; // on subsequent allocation attempts return nil 
}

-(id)copyWithZone:(NSZone*)zone
{
	return self;
}

-(id)retain{
	return self;
}

-(NSUInteger)retainCount
{
	return NSUIntegerMax;
}

-(id)autorelease
{
	return self;
}


#pragma mark REST API functions
-(NSString*)getServerURLForCurrentExperience{
    BlogExperience* experience = [ExperienceConfigurer sharedInstance].currentExperience;
    NSString* url = [NSString stringWithFormat:SERVER_CONTEXT_URL_FORMAT, experience.context];
    return url;
}

-(NSString*)getCompleteURLUsingRelativeURL:(NSString*)relativeURL{
    NSString* url = [self getServerURLForCurrentExperience];
    url = [url stringByAppendingString:@"/"];
    url = [url stringByAppendingString:relativeURL];
    return url;
}

-(NSString*)getOOISearchRequestURLWithNELatitude:(NSString*)neLatitude SWLatitude:(NSString*)swLatitude NELongitude:(NSString*)neLongitude SWLongitude:(NSString*)swLongitude{
    NSString* searchUrl = [NSString stringWithFormat:SEARCH_URL_FORMAT, neLatitude, swLatitude, neLongitude, swLongitude];
    return [self getCompleteURLUsingRelativeURL:searchUrl];
}

-(NSString*)getOoIMetaURLWithMetaID:(NSNumber*)metaID{
    NSString* metaUrl = [NSString stringWithFormat:METADATA_URL_FORMAT,metaID];
    return [self getCompleteURLUsingRelativeURL:metaUrl];
}

-(NSString*)getEmbeddedMapURLForCurrentExperience{
    BlogExperience* experience = [ExperienceConfigurer sharedInstance].currentExperience;
    NSString* url = [NSString stringWithFormat:EMBEDDED_MAP_URL_FORMAT, experience.context];
    return url;
}

-(NSString*)getEmbeddedMapURLWithCenterCoordinate:(CLLocationCoordinate2D)center zoomLevel:(int)zoomLevel{
    NSString *url = [self getEmbeddedMapURLForCurrentExperience];
    url = [url stringByAppendingString:@"?"];
    url = [url stringByAppendingFormat:EMBEDDED_MAP_PARAMETER_FORMAT,[self createEmbedMapJSONWithCenterCoordinate:center zoomLevel:zoomLevel]];
    return url;
}

-(NSString*)createEmbedMapJSONWithCenterCoordinate:(CLLocationCoordinate2D)center zoomLevel:(int)zoomLevel{
    NSString *latitude = [NSString stringWithFormat:@"%lld", [[NSNumber numberWithLongLong:center.latitude * pow(10, 8)] longLongValue]];
    NSString *longitude = [NSString stringWithFormat:@"%lld", [[NSNumber numberWithLongLong:center.longitude * pow(10, 8)] longLongValue]];
    NSString* parameterJSON = [NSString stringWithFormat:EMBEDDED_MAP_PARAMETER_VALUE_JSON_FORMAT, latitude, longitude, zoomLevel]; 
    return parameterJSON;
}

@end
