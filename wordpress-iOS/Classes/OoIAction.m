//
//  OoIAction.m
//  WordPress
//
//  Created by Shakir Ali on 17/07/2012.
//  Copyright (c) 2012 WordPress. All rights reserved.
//

#import "OoIAction.h"

@implementation OoIAction

@synthesize action_id;
@synthesize uri;
@synthesize user_id;
@synthesize date;
@synthesize parent;

-(void)dealloc{
    [uri release];
    [date release];
    [parent release];
    [super dealloc];
}

@end
