//
//  PointLocation.m
//  WordPress
//
//  Created by Shakir Ali on 17/07/2012.
//  Copyright (c) 2012 WordPress. All rights reserved.
//

#import "OoILocation.h"

@implementation OoILocation

@synthesize location_id;
@synthesize source;
@synthesize latitude;
@synthesize longitude;
@synthesize error;
@synthesize parent;
@synthesize timestamp;

//MKAnnotation
//@synthesize coordinate;
//@synthesize title;
//@synthesize subtitle;


-(void)dealloc{
    parent = nil;
    [source release];
    [super dealloc];
}

-(CLLocationCoordinate2D)coordinate{
    CLLocationCoordinate2D pointOfInterestCoordinate;
    pointOfInterestCoordinate.latitude = latitude;
    pointOfInterestCoordinate.longitude = longitude;
    return pointOfInterestCoordinate;
}

-(NSString*)title{
    return @" ";
    //if (title == nil)
    //    return @"Loading...        ";
    //return title;
    /*NSString* text = nil;
    if (parent.meta)
        text = [[parent.meta artist] copy];
    else {
        text = @" ";
    }
    return text; */
}

-(NSString*)subtitle{
    return @" ";
    /*
    NSString* text = nil;
    if (parent.meta)
        text = [[parent.meta description] copy];
    else {
        text = @" ";
    }
    return text;*/
}

- (void)setCoordinate:(CLLocationCoordinate2D)newCoordinate{
    self.latitude = newCoordinate.latitude;
    self.longitude = newCoordinate.longitude;
}


@end
