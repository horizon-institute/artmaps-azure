//
//  RestAPIConnector.h
//  WordPress
//
//  Created by Shakir Ali on 18/07/2012.
//  Copyright (c) 2012 WordPress. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "BlogExperience.h"

@interface RestAPIConnector : NSObject

+(RestAPIConnector*)sharedInstance;
-(NSString*)getOOISearchRequestURLWithNELatitude:(NSString*)neLatitude SWLatitude:(NSString*)swLatitude NELongitude:(NSString*)neLongitude SWLongitude:(NSString*)swLongitude;
-(NSString*)getOoIMetaURLWithMetaID:(NSNumber*)metaID;
-(NSString*)getEmbeddedMapURLWithCenterCoordinate:(CLLocationCoordinate2D)center zoomLevel:(int)zoomLevel;
@end
