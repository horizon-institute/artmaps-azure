//
//  OoIAction.h
//  WordPress
//
//  Created by Shakir Ali on 17/07/2012.
//  Copyright (c) 2012 WordPress. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "ObjectOfInterest.h"

@interface OoIAction : NSObject

@property int action_id;
@property (nonatomic, retain) NSString* uri;
@property int user_id;
@property (nonatomic, retain) NSDate* date;
@property (nonatomic, assign) ObjectOfInterest *parent;
@end
