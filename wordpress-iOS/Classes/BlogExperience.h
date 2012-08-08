//
//  BlogExperience.h
//  WordPress
//
//  Created by Shakir Ali on 19/07/2012.
//  Copyright (c) 2012 WordPress. All rights reserved.
//

#import <Foundation/Foundation.h>

@interface BlogExperience : NSObject

@property (nonatomic, retain) NSString* name;
@property (nonatomic, retain) NSString* context;
@property (nonatomic, retain) NSString* iconPath;

-(NSString*)getTitleForOoIList;
-(NSString*)getTitleForOoI;
@end
