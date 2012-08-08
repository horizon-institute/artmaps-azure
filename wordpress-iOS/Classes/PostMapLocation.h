//
//  PostLocationFrame.h
//  WordPress
//
//  Created by Shakir Ali on 27/07/2012.
//  Copyright (c) 2012 WordPress. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "MapKit/MapKit.h"

@interface PostMapLocation : NSObject

@property CLLocationCoordinate2D center;
@property int zoomLevel;

-(void)setCurrentZoomLevelForMap:(MKMapView*)mapView;

@end
