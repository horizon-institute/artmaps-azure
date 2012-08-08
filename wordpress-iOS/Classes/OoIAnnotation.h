//
//  PointOfInterest.h
//  WordPress
//
//  Created by Shakir Ali on 11/07/2012.
//  Copyright (c) 2012 WordPress. All rights reserved.
//

#import <Foundation/Foundation.h>
#import <MapKit/MKAnnotation.h>

@interface OoIAnnotation : NSObject <MKAnnotation>{
}

@property int OoI_ID;
@property double latitude;
@property double longitude;
@property (nonatomic, retain) NSString* name;
@property (nonatomic, retain) NSString* description;

@end
