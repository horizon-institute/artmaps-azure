//
//  PointOfInterest.m
//  WordPress
//
//  Created by Shakir Ali on 11/07/2012.
//  Copyright (c) 2012 WordPress. All rights reserved.
//

#import "OoIAnnotation.h"

@implementation OoIAnnotation

@synthesize OoI_ID;
@synthesize latitude;
@synthesize longitude;
@synthesize name;
@synthesize description;
@synthesize title;
@synthesize subtitle;

-(void)dealloc{
    [name release];
    [description release];
    [super dealloc];
}

-(CLLocationCoordinate2D)coordinate{
    CLLocationCoordinate2D pointOfInterestCoordinate;
    pointOfInterestCoordinate.latitude = latitude;
    pointOfInterestCoordinate.longitude = longitude;
    return pointOfInterestCoordinate;
}

-(NSString*)title{
    return [[name copy] autorelease];
}

-(NSString*)subtitle{
    return [[description copy] autorelease];
}

- (void)setCoordinate:(CLLocationCoordinate2D)newCoordinate{
    self.latitude = newCoordinate.latitude;
    self.longitude = newCoordinate.longitude;
}

@end
