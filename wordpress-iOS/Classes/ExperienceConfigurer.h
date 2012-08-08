//
//  ExperienceConfigurer.h
//  WordPress
//
//  Created by Shakir Ali on 19/07/2012.
//  Copyright (c) 2012 WordPress. All rights reserved.
//

#import <Foundation/Foundation.h>
#import "BlogExperience.h"

@class Blog;

@interface ExperienceConfigurer : NSObject
+(ExperienceConfigurer*)sharedInstance;
@property (nonatomic, retain) BlogExperience* currentExperience;
@property (nonatomic, retain) Blog *selectedBlog;
-(NSArray*)getRegisteredBlogExperiences;
@end
