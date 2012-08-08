//
//  PointLocation.h
//  WordPress
//
//  Created by Shakir Ali on 17/07/2012.
//  Copyright (c) 2012 WordPress. All rights reserved.
//

#import <UIKit/UIKit.h>
#import <MapKit/MKAnnotation.h>
#import "ObjectOfInterest.h"
#import "REVClusterPin.h"

@interface OoILocation : REVClusterPin

@property int location_id;
@property (nonatomic, retain) NSString* source;
@property double latitude;
@property double longitude;
@property int error;
@property long timestamp;
@property (nonatomic, assign) ObjectOfInterest* parent;


@end
