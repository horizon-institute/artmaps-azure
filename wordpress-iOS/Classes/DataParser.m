//
//  DataParser.m
//  WordPress
//
//  Created by Shakir Ali on 21/07/2012.
//  Copyright (c) 2012 WordPress. All rights reserved.
//

#import "DataParser.h"
#import "OoILocation.h"
#import "OoIAction.h"

@implementation DataParser

+(ObjectOfInterest*)getOoIFromDictionary:(NSDictionary*)dict{
    ObjectOfInterest* ooi = [[ObjectOfInterest alloc] init];
    ooi.OoI_id = [[dict objectForKey:@"ID"] intValue];
    ooi.uri = [dict objectForKey:@"URI"];
    
    //get locations
    NSArray* locations = (NSArray*) [dict objectForKey:@"locations"];
    NSMutableArray* ooiLocations = [[NSMutableArray alloc] initWithCapacity:locations.count];
    for (int i = 0; i < locations.count; i++){
        OoILocation *location = [self getOoILocationFromDictionary:[locations objectAtIndex:i]];
        location.parent = ooi;
        [ooiLocations addObject:location];
    }
    [ooi setOoiLocations:ooiLocations];
    [ooiLocations release];
    
    //get actions
    NSArray* actions = (NSArray*) [dict objectForKey:@"actions"];
    NSMutableArray* ooiActions = [[NSMutableArray alloc] initWithCapacity:actions.count];
    for ( int i = 0; i < actions.count; i++){
        OoIAction *action = [self getOoIActionFromDictionary:[actions objectAtIndex:i]];
        action.parent = ooi;
        [ooiActions addObject:action];
    }
    [ooi setOoiActions:ooiActions];
    [ooiActions release];
    
    return ([ooi autorelease]);
}

+(OoILocation*)getOoILocationFromDictionary:(NSDictionary*)dict{
    OoILocation* location = [[OoILocation alloc] init];
    location.location_id = [[dict objectForKey:@"ID"] intValue];
    location.error = [[dict objectForKey:@"error"] intValue];
    location.latitude = [[dict objectForKey:@"latitude"] doubleValue] / pow(10, 8);
    location.longitude = [[dict objectForKey:@"longitude"] doubleValue] / pow(10, 8);
    location.source = [dict objectForKey:@"source"];
    location.timestamp = [[dict objectForKey:@"timestamp"] longValue];
    return ([location autorelease]);
}

+(OoIAction*)getOoIActionFromDictionary:(NSDictionary*)dict{
    OoIAction* action = [[OoIAction alloc] init];
    action.action_id = [[dict objectForKey:@"ID"] intValue];
    action.uri = [dict objectForKey:@"URI"]; 
    action.user_id = [[dict objectForKey:@"userID"] intValue];
    //action.date = [[dict objectForKey:@"datetime"] date
    return ([action autorelease]);
}

+(OoIMeta*)getOoIMetaFromDictionary:(NSDictionary*)dict{
    OoIMeta* meta = [[OoIMeta alloc] init];
    meta.artist = [dict objectForKey:@"artist"];
    meta.artistdate = [dict objectForKey:@"artistdate"];
    meta.artworkdate = [dict objectForKey:@"artworkdate"];
    meta.imageurl = [dict objectForKey:@"imageurl"];
    meta.reference = [dict objectForKey:@"reference"];
    meta.title = [dict objectForKey:@"title"];
    return [meta autorelease];
}

@end
