//
//  PostLocationFrame.m
//  WordPress
//
//  Created by Shakir Ali on 27/07/2012.
//  Copyright (c) 2012 WordPress. All rights reserved.
//

#import "PostMapLocation.h"
#import "MapHelper.h"

@implementation PostMapLocation

@synthesize center;
@synthesize zoomLevel;

-(void)setCurrentZoomLevelForMap:(MKMapView*)mapView{
    self.zoomLevel = [MapHelper getMapZoomLevel:mapView];
}

@end