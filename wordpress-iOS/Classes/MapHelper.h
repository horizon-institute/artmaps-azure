//
//  MapHelper.h
//  WordPress
//
//  Created by Shakir Ali on 27/07/2012.
//  Copyright (c) 2012 WordPress. All rights reserved.
//

#import <Foundation/Foundation.h>
#import <MapKit/MapKit.h>

extern int const MAX_GOOGLE_ZOOM_LEVEL;

@interface MapHelper : NSObject

+(CLLocationCoordinate2D)calculateNEMapCoordinates:(MKMapView*)mapView;
+(CLLocationCoordinate2D)calculateSWMapCoordinates:(MKMapView*)mapView;
+(int)getMapZoomLevel:(MKMapView*)mapView;

@end
