//
//  MapHelper.m
//  WordPress
//
//  Created by Shakir Ali on 27/07/2012.
//  Copyright (c) 2012 WordPress. All rights reserved.
//

#import "MapHelper.h"

int const MAX_GOOGLE_ZOOM_LEVEL = 21;

@implementation MapHelper

+(CLLocationCoordinate2D)calculateNEMapCoordinates:(MKMapView*)mapView{
    MKMapRect mRect = mapView.visibleMapRect;
    MKMapPoint neMapPoint = MKMapPointMake(MKMapRectGetMaxX(mRect), mRect.origin.y);
    return MKCoordinateForMapPoint(neMapPoint);
}

+(CLLocationCoordinate2D)calculateSWMapCoordinates:(MKMapView*)mapView{
    MKMapRect mRect = mapView.visibleMapRect;
    MKMapPoint swMapPoint = MKMapPointMake(mRect.origin.x, MKMapRectGetMaxY(mRect));
    return MKCoordinateForMapPoint(swMapPoint);
}

+(int)getMapZoomLevel:(MKMapView*)mapView{
    MKZoomScale zoomScale = (CGFloat)(mapView.bounds.size.width / mapView.visibleMapRect.size.width); 
    int zoomLevel = MAX(0, MAX_GOOGLE_ZOOM_LEVEL - abs(log2(zoomScale))); 
    return zoomLevel;
}

@end
