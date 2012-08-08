//
//  ObjectOfInterest.h
//  WordPress
//
//  Created by Shakir Ali on 17/07/2012.
//  Copyright (c) 2012 WordPress. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "OoIMeta.h"

@interface ObjectOfInterest : NSObject
@property int OoI_id;
@property (nonatomic, retain) NSString* uri;
@property (nonatomic, retain) NSArray* ooiLocations;
@property (nonatomic, retain) NSArray* ooiActions;
@property (nonatomic, retain) OoIMeta* meta;
@end
